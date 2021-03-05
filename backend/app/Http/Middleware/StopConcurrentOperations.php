<?php

namespace App\Http\Middleware;

use App\Models\News;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class StopConcurrentOperations {

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $id = current($request->route()->parameters());

        if(auth()->user() && !empty($id)) {
            $news = News::withTrashed()->findOrFail($id);

            if($news->is_editing && Carbon::parse($news->updated_at)->diffInMinutes(Carbon::now()) < 15 && $news->editing_by != auth()->user()->id) {
                abort(403, 'Статья в данный момент используется другим автором: '.User::withTrashed()->findOrFail($news->editing_by)->name);
            }else {
                $news->is_editing = TRUE;
                $news->editing_by = auth()->user()->id;
                $news->save();
            }
        }

        return $next($request);
    }

}
