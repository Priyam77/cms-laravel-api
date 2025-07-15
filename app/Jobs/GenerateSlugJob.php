<?php

namespace App\Jobs;

use App\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateSlugJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $articleId;

    public function __construct($articleId)
    {
        $this->articleId = $articleId;
    }

    public function handle()
    {
        $article = Article::find($this->articleId);
        if (!$article) return;

        $prompt = "Generate a short SEO-friendly slug for the following article:\n\nTitle: {$article->title}\nContent: {$article->content}";

        $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 20,
        ]);

        if (isset($response['choices'][0]['message']['content'])) {
            $slugText = trim($response['choices'][0]['message']['content']);
            $slug = Str::slug($slugText);
            $article->update(['slug' => $slug]);
        } else {
            \Log::error('OpenAI API failed to return slug content.', ['response' => $response->json()]);
        }
    }
}


// namespace App\Jobs;

// use App\Article;
// use Illuminate\Bus\Queueable;
// use Illuminate\Support\Str;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Foundation\Bus\Dispatchable;

// class GenerateSlugJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     protected $articleId;

//     public function __construct($articleId)
//     {
//         $this->articleId = $articleId;
//     }

//     public function handle()
//     {
//         $article = Article::find($this->articleId);
//         if (!$article) return;

//         // Fallback local slug generation
//         $slug = Str::slug($article->title) . '-' . $article->id;
//         $article->update(['slug' => $slug]);

//         \Log::info("Slug generated locally for Article ID {$this->articleId}: $slug");
//     }
// }
