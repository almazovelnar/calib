<?php

namespace App\Traits;

use App\Models\News;
use App\Models\User;
use Carbon\Carbon;
use Eloquent;
use App\Models\Log;
use Illuminate\Support\Str;

trait Loggable {

    public $user;

    public static function boot() : void {
        parent::boot();

        static::created(function ($model) {
            $model->logEvent('created', $model);
        });

        static::saving(function ($model) {
            $model->logEvent('updated', $model);
        });

        static::deleted(function ($model) {
            $model->logEvent('deleted', $model);
        });
    }

    /**
     * @param string $action
     * @param        $model
     *
     * @throws \Exception
     */
    public function logEvent(string $action, $model) : void {
        if($model instanceof Log) {
            return;
        }

        $dirty = $model->getDirty();

        if($model instanceof User && count($dirty) === 1 && array_key_exists('remember_token', $dirty)) {
            return;
        }

        if($model instanceof User && count($dirty) === 2 && array_key_exists('remember_token', $dirty) && array_key_exists('updated_at', $dirty)) {
            return;
        }

        $properties = $this->generateProperties($action, $model);

        if(count($properties) == 0) {
            return;
        }

        if(empty($properties['old']) && empty($properties['new'])) {
            return;
        }

        $logSaved = Log::create([
            'action'       => $action,
            'description'  => '',
            'subject_type' => get_class($model),
            'news_id'      => $model->id,
            'user_id'      => auth()->check() ? auth()->user()->id : NULL,
            'properties'   => $properties,
            'action_at'    => Carbon::now()->format('Y-m-d H:i:s'),
            'agent'        => $_SERVER['HTTP_USER_AGENT'] ?? 'system',
            'ip'           => ip(),
        ]);

        if(!$logSaved instanceof Log) {
            throw new \Exception('Can\'t save log');
        }
    }

    /**
     * @param string $action
     * @param        $model Eloquent
     *
     * @throws \Exception
     * @return string
     */
    public function generateProperties(string $action, $model) : array {
        switch ($action) {
            case 'retrieved':
                return '{}';
            case 'saved':
            case 'created':
                return ['old' => [], 'new' => $model->attributesToArray()];
            case 'deleted':
                return ['old' => $model->attributesToArray(), 'new' => []];
            case 'updated':
            {
                $old = [];

                foreach($new = $model->getDirty() as $field => $changed) {
                    if(in_array($field, ['password', 'refresh_tokens', 'updated_at','is_editing','edited_by','view'])) {
                        return [];
                    }
                    $old[$field] = $model->getOriginal($field) ?? '';
                }

                return ['old' => $old, 'new' => $new];
            }
            default:
                throw new \Exception('Unexpected log type');
        }
    }

}
