<?php

namespace App\Jobs;

use App\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateSummaryJob implements ShouldQueue
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

        $prompt = "Summarize the following article content in 2-3 sentences:\n\n{$article->content}";

        $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 100,
        ]);

        if (isset($response['choices'][0]['message']['content'])) {
            $summary = trim($response['choices'][0]['message']['content']);
            $article->update(['summary' => $summary]);
        } else {
            \Log::error('OpenAI API failed to return summary content.', ['response' => $response->json()]);
        }
    }
}

// namespace App\Jobs;

// use App\Article;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Foundation\Bus\Dispatchable;

// class GenerateSummaryJob implements ShouldQueue
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

//         // Fallback local summary: first 30 words from content
//         $plainText = strip_tags($article->content);
//         $words = str_word_count($plainText, 1);
//         $summary = implode(' ', array_slice($words, 0, 30)) . '...';

//         $article->update(['summary' => $summary]);

//         \Log::info("Summary generated locally for Article ID {$this->articleId}: $summary");
//     }
// }
