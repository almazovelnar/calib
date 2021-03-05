<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\News;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ReCache extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache all homepage';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle() : void {
        $urls = [
            'https://caliber.az'
        ];

        $this->exec('rm -rf /etc/nginx/cache' );

        $post = News::whereShow(TRUE)
                    ->whereHas('user')
                    ->where('published_at', '<=', now()->format('Y-m-d H:i:s'))
                    ->whereTopLeft(TRUE)
                    ->orderByDesc('top_left_at')
                    ->setEagerLoads([])
                    ->with('category:id,name,slug')
                    ->get()
                    ->map(function (News $post) {
                        return $post->only([
                            'slug',
                        ]);
                    })
                    ->first();

        if($post) {
            $urls[] = 'https://caliber.az/post/' . $post['slug'];
        }

        $post = News::whereShow(TRUE)
                    ->whereHas('user')
                    ->where('published_at', '<=', now()->format('Y-m-d H:i:s'))
                    ->whereTopRight(TRUE)
                    ->orderByDesc('top_right_at')
                    ->setEagerLoads([])
                    ->with('category:id,name,slug')
                    ->get()
                    ->map(function (News $post) {
                        return $post->only([
                            'slug',
                        ]);
                    })
                    ->first();

        if($post) {
            $urls[] = 'https://caliber.az/post/' . $post['slug'];
        }

        $newses = News::whereShow(TRUE)
                      ->whereHas('user')
                      ->where('published_at', '<=', now()->format('Y-m-d H:i:s'))
                      ->limit(95)
                      ->offset(0)
                      ->orderByDesc('published_at')
                      ->setEagerLoads([])
                      ->whereTopRight(FALSE)
                      ->whereTopLeft(FALSE)
                      ->whereHas('category')
                      ->whereHas('user')
                      ->with('category:id,name,slug')
                      ->get()
                      ->pluck('slug')
                      ->toArray();

        foreach($newses as $news) {
            $urls[] = 'https://caliber.az/post/' . $news;
        }

        foreach(Category::whereShow(TRUE)->get() as $category) {
            $urls[] = 'https://caliber.az/category/' . $category->slug;

            $newses = $category->news()
                               ->whereNull('deleted_at')
                               ->whereShow(TRUE)
                               ->setEagerLoads([])
                               ->where('published_at', '<=', now()->format('Y-m-d H:i:s'))
                               ->orderByDesc('published_at')
                               ->whereHas('user')
                               ->offset(0)
                               ->limit(18)
                               ->get();

            foreach($newses as $news) {
                $urls[] = 'https://caliber.az/post/' . $news->slug;
            }
        }

        foreach($urls as $url) {
            if($this->exec("curl -I $url -H \"cachepurge: true\"")['return'] != 0) {
                echo 'Failed for '.$url.PHP_EOL;
            }
        }
    }

    public function exec(string $cmd, $input = '') {
        $home = getenv('HOME');

        if(!is_writable($home)) {
            $cmd = 'export HOME=/tmp && ' . $cmd;
        }

        $process = proc_open($cmd, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);

        if(FALSE === $process) {
            throw new \Exception('Cannot obtain resource for process to convert file');
        }

        fwrite($pipes[0], $input);
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $rtn = proc_close($process);

        return [
            'stdout' => $stdout,
            'stderr' => $stderr,
            'return' => $rtn,
        ];
    }

}
