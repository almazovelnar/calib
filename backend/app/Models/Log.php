<?php

namespace App\Models;

use App\Traits\ParsableAgent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Log extends Model {

    use SoftDeletes, ParsableAgent;

    public $timestamps = FALSE;

    protected $guarded = ['id'];

    protected $casts = [
        'properties' => 'array',
    ];

    protected $with = [
        'causer'
    ];

    public function causer() : BelongsTo {
        return $this->belongsTo(User::class,'user_id');
    }

}
