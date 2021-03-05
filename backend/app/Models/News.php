<?php

namespace App\Models;

use App\Jobs\UpdateCache;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class News extends Model {

    use HasFactory, SoftDeletes, Loggable;

    protected $guarded = ['id'];

    protected $casts = [
        'show'         => 'boolean',
        'top_left'     => 'boolean',
        'top_right'    => 'boolean',
        'published_at' => 'datetime',
        'on_index' => 'boolean',
        'show_img' => 'boolean'
    ];

    public $timestamps = false;

    protected $with = ['logs','gallery'];

    protected static function boot() {
        parent::boot();

        static::created(function($news){
            $news->logEvent('created', $news);

            if(empty($news->slug)) {
                $news->slug = Str::slug($news->name).'-'.$news->id;
                $news->save();

                dispatch(new UpdateCache($news->id));
            }
        });

        static::saving(function ($model) {
            $model->logEvent('updated', $model);
        });

        static::deleted(function ($model) {
            $model->logEvent('deleted', $model);
        });
    }

    public function getImageAttribute(?string $value) : string {
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

    public function getNameAttribute(?string $value) : string {
        return html_entity_decode(preg_replace('/\"([^<>]*?)\"(?=(?:[^>]*?(?:<|$)))/u', "&laquo;\\1&raquo;", preg_replace('/\'([^<>]*?)\'(?=(?:[^>]*?(?:<|$)))/u', "&laquo;\\1&raquo;", $value ?? '')));
    }

    public function getContentAttribute(?string $value) : string {
        return html_entity_decode($value);

        return preg_replace('#<script(.*?)>(.*?)</script>#is', '', html_entity_decode($value));
    }

    public function category() : BelongsToMany {
        return $this->belongsToMany(Category::class);
    }
    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function logs() : HasMany {
        return $this->hasMany(Log::class);
    }

    public function gallery(): HasMany {
        return $this->hasMany(NewsGallery::class);
    }
}
