<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsGallery extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getFilenameAttribute(?string $value) : string {
        \URL::forceRootUrl('https://caliber.az');

        if(!parse_url($value, PHP_URL_SCHEME)) {
            $url = url(\Storage::url($value));

            \URL::forceRootUrl('https://admin.caliber.az');

            return $url;

            //            $path = storage_path('app/public/'.$value);
            //
            //            $type = pathinfo($path, PATHINFO_EXTENSION);
            //            $data = file_get_contents($path);
            //            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            //
            //            return $base64;
        }

        return $value;
    }

    public function getUrlAttribute() {
        \URL::forceRootUrl('https://caliber.az');

        if(!parse_url($this->filename, PHP_URL_SCHEME)) {
            $url = url(\Storage::url($this->filename));

            \URL::forceRootUrl('https://admin.caliber.az');

            return $url;

            //            $path = storage_path('app/public/'.$value);
            //
            //            $type = pathinfo($path, PATHINFO_EXTENSION);
            //            $data = file_get_contents($path);
            //            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            //
            //            return $base64;
        }

        return $this->filename;
    }
}
