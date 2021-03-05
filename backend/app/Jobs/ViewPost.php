<?php

namespace App\Jobs;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ViewPost implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $postId;

    public $ip;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $postId, string $ip) {
        $this->postId = $postId;
        $this->ip     = $ip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $news = News::findOrFail($this->postId);

        /** @var $news \Illuminate\Database\Eloquent\Model */
        $news->increment('view');
    }

}
