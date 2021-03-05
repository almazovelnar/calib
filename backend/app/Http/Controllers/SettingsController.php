<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Settings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettingsController extends Controller {

    public function edit() : View {
        $this->authorize('settings_update');

        $settings = Settings::all();

        return view('settings', compact('settings'));
    }

    public function editAbout() : View {
        $this->authorize('settings_update');

        $settings = Settings::all();

        return view('about-us', compact('settings'));
    }

    public function editBanner() : View {
        $this->authorize('settings_update');

        $settings = Settings::all();

        return view('banner', compact('settings'));
    }

    public function update(Request $request) : RedirectResponse {
        $this->authorize('settings_update');

        $request->validate([
            'usd'           => 'sometimes|required|string|max:255',
            'euro'          => 'sometimes|required|string|max:255',
            'oil'           => 'sometimes|required|string|max:255',
            'covid_total'   => 'sometimes|required|string|max:255',
            'covid_baku'    => 'sometimes|required|string|max:255',
            'weather_baku'  => 'sometimes|required|string|max:255',
            'weather_ganca' => 'sometimes|required|string|max:255',
            'red_title'     => 'nullable|string|max:255',
            'red_tag'       => 'nullable|string|max:255',
            'red_date'      => 'nullable|date_format:Y-m-d\TH:i',
            'lat'           => 'sometimes|required|string|max:255',
            'long'          => 'sometimes|required|string|max:255',
            'banner_height' => 'sometimes|numeric',
            'banner_text'   => 'sometimes|nullable'
        ]);

        $request->merge([
            'red_date' => Carbon::parse($request->get('red_date'))->format("Y-m-d H:i")
        ]);

        Settings::where('key', '=', 'red_title')->update([
            'value' => NULL
        ]);

        Settings::where('key', '=', 'red_tag')->update([
            'value' => NULL
        ]);

        Settings::where('key', '=', 'red_date')->update([
            'value' => NULL
        ]);

        foreach($request->only([
            'usd',
            'euro',
            'oil',
            'covid_total',
            'covid_baku',
            'weather_baku',
            'weather_ganca',
            'red_title',
            'red_tag',
            'red_date',
            'long',
            'lat',
            'banner_height',
            'banner_text',
            'about_us_text',
        ]) as $key => $value) {
            $m = Settings::where('key', '=', $key)->get()->first();

            $changes   = $m->changes;
            $changes[] = [
                'old'  => $m->value,
                'new'  => $value,
                'date' => now()->format('Y-m-d H:i:s')
            ];

            $m->update([
                'value'   => empty($value) ? NULL : $value,
                'changes' => $changes
            ]);
        }
        sleep(1);

        $this->exec("curl -I https://caliber.az -H \"cachepurge: true\"");
        $this->exec("curl -I https://caliber.az/about-us -H \"cachepurge: true\"");

        return redirect()->back()->with('success', 'Updated.');
    }

    public function apiList() : Response {
        return response(Settings::all()->map->only(['key', 'value'])->pluck('value', 'key'));
    }

    public function stats(Request $request) {
        $request->validate([
            'from' => 'sometimes|required|date_format:Y-m-d',
            'to'   => 'sometimes|required|date_format:Y-m-d'
        ]);

        if($request->isXmlHttpRequest()) {

            $users = User::all()->map(function ($user) use ($request) {
                if($request->has('from') && $request->has('to')) {
                    $user->article_count = $user->news()->whereBetween('published_at', [
                        Carbon::parse($request->get('from'))->startOfDay()->format('Y-m-d H:i:s'),
                        Carbon::parse($request->get('to'))->endOfDay()->format('Y-m-d H:i:s')
                    ])->count();
                    $user->hits          = $user->news()->whereBetween('published_at', [
                        Carbon::parse($request->get('from'))->startOfDay()->format('Y-m-d H:i:s'),
                        Carbon::parse($request->get('to'))->endOfDay()->format('Y-m-d H:i:s')
                    ])->sum('view');
                }else {
                    $user->article_count = $user->news()->count();
                    $user->hits          = $user->news()->sum('view');
                }

                return $user->only([
                    'id',
                    'name',
                    'article_count',
                    'hits'
                ]);
            });

            return json_encode([
                'data' => $users->toArray()
            ]);
        }

        return view('stats');
    }

    public function popular(Request $request) {
        if($request->isXmlHttpRequest()) {

            $news = News::all()->map(function($news) {
                $news->date = Carbon::parse($news->published_at)->format('Y-m-d H:i:s');

                return $news->only([
                    'id',
                    'name',
                    'date',
                    'view'
                ]);
            });

            return json_encode([
                'data' => $news->toArray()
            ]);
        }

        return view('popular');
    }
}
