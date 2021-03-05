<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Notifications\UserAdded;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller {

    /**
     * List of users
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return false|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|string
     */
    public function index(Request $request) {
        $this->authorize('user_read');

        if($request->isXmlHttpRequest()) {
            $admins = User::all();

            return json_encode([
                'data' => $admins->toArray()
            ]);
        }

        return view('user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() : View {
        $this->authorize('user_create');

        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) : RedirectResponse {
        $this->authorize('user_create');

        $request->merge([
           'username' => Str::slug($request->get('name'))
        ]);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'position' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'username' => 'required|unique:users'
        ]);

        $password = $request->input('password');

        $user = User::create([
            'name'     => $request->get('name'),
            'email'    => $request->get('email'),
            'password' => Hash::make($password),
            'username' => Str::slug($request->get('name')),
            'position' => $request->get('position'),
        ]);

        if(!$user instanceof User) {
            return redirect()->back()->with('error', 'Error.');
        }

        return redirect()->route('user.index')->with('success', 'New admin added.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     *
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user) : RedirectResponse {
        $this->authorize('user_delete');

        $slug = $user->username;

        $user->delete();

        $this->exec("curl -I https://caliber.az/user/{$slug}/posts -H \"cachepurge: true\"");

        return redirect()->route('user.index')->with('success', 'Admin deleted.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit() : View {
        return view('user.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request) : RedirectResponse {
        $user = auth()->user();

        $request->validate([
            'name'         => 'required|string',
            'position'     => 'required|string',
            'old_password' => 'nullable|string',
            'password'     => 'nullable|string|min:8|confirmed',
        ]);

        $fields = $request->only(['name', 'position']);

        $passwordUpdated = FALSE;

        if(!empty($request->get('old_password')) && !empty($request->get('password')) && !empty($request->get('password_confirmation'))) {

            if(!Hash::check($request->get('old_password'), $user->password)) {
                return redirect()->back()->with('error', 'Old password must be correct.');
            }

            if(Hash::check($request->get('password'), $user->password)) {
                return redirect()->back()->with('error', 'Old password can\'t be the same with new one.');
            }

            $fields['password'] = Hash::make($request->get('password'));

            $passwordUpdated = TRUE;
        }

        if(!$user->update($fields)) {
            return redirect()->back()->with('error', 'Error.');
        }

        if($passwordUpdated) {
            auth()->logout();

            session()->invalidate();

            return redirect()->route('login')->with('succes', 'Password updated');
        }

        return redirect()->back()->with('success', 'Updated.');
    }

    /**
     * Edited permission
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function editPermission(User $user) {
        $this->authorize('user_update');

        return view('user.edit-permission', compact('user'));
    }

    public function updatePermission(User $user, Request $request) {
        $this->authorize('user_update');

        $request->validate([
            'permission'   => 'required|array',
            'permission.*' => 'sometimes|accepted',
        ]);

        $updated = $user->update($request->only('permission'));

        if(!$updated) {
            throw new \Exception('Can\'t update user permissions');
        }

        return redirect()->route('user.index')->with('success', 'Permissions updated.');
    }

    public function apiPosts(User $user, Request $request) {
        $offset = $request->query('limit') * ( $request->query('page') - 1 );
        $limit  = $request->get('limit');

        $posts = $user->news()
                          ->whereShow(TRUE)
                          ->where('published_at', '<=', now()->format('Y-m-d H:i:s'))
                          ->limit($limit)
                          ->offset($offset)
                          ->orderByDesc('published_at')
                          ->setEagerLoads([])
                          ->with('category:id,name,slug')
                          ->get();

        return response($posts->map(function (News $post) {
            $post->date = Carbon::parse($post->published_at)->translatedFormat('d M Y, H:i');

            return $post->only([
                'slug',
                'image',
                'name',
                'category',
                'date',
            ]);
        }));
    }

    public function clearNotifications() {
        \DB::table('notifications')->truncate();

        return back();
    }
}
