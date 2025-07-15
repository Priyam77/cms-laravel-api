<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Article;
use Illuminate\Support\Facades\Auth;
use App\Jobs\GenerateSlugJob;
use App\Jobs\GenerateSummaryJob;

class ArticleController extends Controller
{
    
// public function index(Request $request)
// {
//     $query = Article::query();

//     if ($request->has('status')) {
//         $query->where('status', $request->status);
//     }

//     if ($request->has('category_id')) {
//         $query->whereHas('categories', function ($q) use ($request) {
//             $q->where('categories.id', $request->category_id);
//         });
//     }

//     if ($request->has('start_date') && $request->has('end_date')) {
//         $query->whereBetween('articles.published_date', [
//             $request->start_date,
//             $request->end_date
//         ]);
//     }

//     return response()->json($query->with('categories')->get());
// }
public function index(Request $request)
{
    $query = Article::query();

    $user = auth()->user();

    // Show only own articles if user is an author
    if ($user->role === 'author') {
        $query->where('author_id', $user->id);
    }

    if ($request->has('status')) {
        $query->where('status', $request->status);
    }

    if ($request->has('category_id')) {
        $query->whereHas('categories', function ($q) use ($request) {
            $q->where('categories.id', $request->category_id);
        });
    }

    if ($request->has('start_date') && $request->has('end_date')) {
        $query->whereBetween('articles.published_date', [
            $request->start_date,
            $request->end_date
        ]);
    }

    return response()->json($query->with('categories')->get());
}





public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string',
        'content' => 'required|string',
        'category_ids' => 'array',
        'category_ids.*' => 'exists:categories,id',
    ]);

    $article = Article::create([
        'title' => $request->title,
        'content' => $request->content,
        'status' => 'draft',
        'author_id' => auth()->id(),
        'published_date' => now(),
    ]);

    // ✅ Link categories to the article
    if ($request->has('category_ids')) {
        $article->categories()->sync($request->category_ids);
    }

    return response()->json(['message' => 'Article created', 'article' => $article]);
}

public function update(Request $request, $id)
{
    $request->validate([
        'title' => 'required|string',
        'content' => 'required|string',
        'category_ids' => 'array',
        'category_ids.*' => 'exists:categories,id',
    ]);

    $article = Article::findOrFail($id);

    // Only allow the author to update their own articles
    if (auth()->user()->role === 'author' && $article->author_id !== auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $article->update([
        'title' => $request->title,
        'content' => $request->content,
    ]);

    if ($request->has('category_ids')) {
        $article->categories()->sync($request->category_ids);
    }

    return response()->json(['message' => 'Article updated', 'article' => $article]);
}

public function destroy($id)
{
    $article = Article::findOrFail($id);

    // Only allow the author to delete their own articles
    if (auth()->user()->role === 'author' && $article->author_id !== auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $article->delete();

    return response()->json(['message' => 'Article deleted']);
}


}
