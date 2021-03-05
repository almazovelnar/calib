<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Storage;

class Controller extends BaseController {

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param \Illuminate\Http\File|\Illuminate\Http\UploadedFile|string $file
     * @param null|string                                                $oldFile
     * @param string                                                     $disk
     *
     * @return string
     */
    public function uploadAsset($file, string $oldFile = null, string $disk = 'public'): string {
        if($oldFile) {
            Storage::disk($disk)->delete(str_replace('/storage/','',$oldFile));
        }

        $fileName = strtoupper(Str::uuid()).'.'.$file->getClientOriginalExtension();

        Storage::disk($disk)->putFileAs(date('Y/m/d'),$file, $fileName);

        if (strncasecmp($file->getClientOriginalName(),'cbr_',4) == 0) {
            $img = Image::make($path = storage_path('app/public/'.date('Y/m/d/').$fileName));

            $watermark = Image::make(public_path('watermark.png'));
            $watermark->resize($img->width() * 12 / 100,$img->width() * 12 / 100);

            $img->insert($watermark, 'bottom-right', 10, 10);
            $img->insert($watermark, 'bottom-left', 10, 10);
            $img->insert($watermark, 'top-right', 10, 10);
            $img->insert($watermark, 'top-left', 10, 10);

            $img->save($path);
        }

        return date('Y/m/d/').$fileName;
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

        return [
            'stdout' => $stdout,
            'stderr' => $stderr,
            'return' => $rtn,
        ];
    }

}
