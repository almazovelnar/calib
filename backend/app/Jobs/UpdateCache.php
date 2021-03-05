<?php

namespace App\Jobs;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCache implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $postId;

    public $ip;

    public $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $id) {
        $this->id = $id;
    }

    public function exec(string $cmd, $input = '') {
        $home = getenv('HOME');

        if (!is_writable($home)) {
            $cmd = 'export HOME=/tmp && '.$cmd;
        }

        $process = proc_open($cmd, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);

        if (false === $process) {
            throw new \Exception('Cannot obtain resource for process to convert file');
        }

        fwrite($pipes[0], $input);
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $rtn = proc_close($process);

        if($rtn != 0) {
            throw new \Exception('Can\'t update cache: '. $stderr);
        }

        return [
            'stdout' => $stdout,
            'stderr' => $stderr,
            'return' => $rtn,
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        sleep(1);

        $news = News::findOrFail($this->id);

        cache()->tags('news')->flush();

        $this->exec("curl -I https://caliber.az -H \"cachepurge: true\"")['return'];

        foreach($news->category as $category) {
            $this->exec("curl -I https://caliber.az/category/{$category->slug} -H \"cachepurge: true\"");
        }

        $this->exec("curl -I https://caliber.az/post/{$news->slug} -H \"cachepurge: true\"");
    }

}
