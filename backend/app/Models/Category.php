<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model {

    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'show' => 'boolean',
    ];

    public function getImageAttribute(?string $value) : string {
        \URL::forceRootUrl('caliber.az');

        $url = url(\Storage::url($value));

        \URL::forceRootUrl('https://admin.caliber.az');

        return $url;
    }

    public function news() : BelongsToMany {
        return $this->belongsToMany(News::class);
    }

    protected static function boot() {
        parent::boot(); // TODO: Change the autogenerated stub

        static::deleting(function ($category) {
            foreach($category->news()->get() as $news) {
                if($news->category->count() == 1) {
                    $news->update([
                        'show' => FALSE
                    ]);
                }
            }
        });
    }

}
