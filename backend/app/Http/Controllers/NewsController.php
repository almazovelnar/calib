<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateCache;
use App\Jobs\ViewPost;
use App\Models\Category;
use App\Models\News;
use App\Models\User;
use App\Notifications\NewsAdded;
use App\Notifications\NewsPublished;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewsController extends Controller {

    public function __construct() {
        $this->middleware('concurrent.operations', ["only" => ["edit", "update"]]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request) : JsonResponse {
        $request->validate([
            'upload'      => 'required|image|max:2048|mimes:jpg,jpeg,bmp,png',
            'watermark'   => 'sometimes|required|accepted',
            'ckCsrfToken' => 'required|string'
        ]);

        $uploaded = $this->uploadAsset($request->file('upload'));

        \URL::forceRootUrl('https://caliber.az');
        $url =  url(\Storage::url($uploaded));
        \URL::forceRootUrl('https://admin.caliber.az');

        return response()->json([
            'uploaded' => 1,
            'fileName' => $uploaded,
            'url'      => $url
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImageDropzone(Request $request) : JsonResponse {
        $request->validate([
            'file'      => 'required|image|max:2048|mimes:jpg,jpeg,bmp,png',
        ]);

        $uploaded = $this->uploadAsset($request->file('file'));

        return response()->json([
            'uploaded' => 1,
            'fileName' => $uploaded,
            'url'      => url(\Storage::url($uploaded))
        ]);
    }

    public function show() {
        return redirect(url('/news'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return false|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|string
     */
    public function index(Request $request) {
        $this->authorize('news_read');

        if($request->has('deleted') && $request->query('deleted') === 'true') {
            $this->authorize('news_deleted');
        }

        if(auth()->user()->hasPermissionFor('news_all')) {
            $news = News::setEagerLoads([])->whereHas('user')->with('category:id,name,slug')->with('user:id,name,username');

        }else {
            $news = auth()->user()->news()->setEagerLoads([])->whereHas('user')->with('category:id,name,slug')->with('user:id,name,username');
        }

        if($request->has('deleted') && $request->query('deleted') === 'true') {
            if(!auth()->user()->hasPermissionFor('news_deleted')) {
                abort(403);
            }

            $news = $news->withTrashed()->whereNotNull('deleted_at');
        }

        if($request->has('published')) {
            if($request->query('published') === 'true') {
                $news->whereShow(TRUE);
            }else {
                if($request->query('published') === 'false') {
                    $news->whereShow(FALSE);
                }
            }
        }

        if($request->has('name')) {
            $keywords = explode(' ', $request->query('name'));

            $news->where(function ($q) use ($keywords) {
                $q->where('name', 'ilike', "%$keywords[0]%")
                  ->orWhere('title', 'ilike', "%$keywords[0]%")
                  ->orWhere('description', 'ilike', "%$keywords[0]%")
                  ->orWhere('content', 'ilike', "%$keywords[0]%");

                foreach($keywords as $index => $word) {
                    if($index === 0) {
                        continue;
                    }

                    $q->orWhere('name', 'ilike', "%$word%")
                      ->orWhere('title', 'ilike', "%$word%")
                      ->orWhere('description', 'ilike', "%$word%")
                      ->orWhere('content', 'ilike', "%$word%");
                }
            });
        }

        $news = $news->orderByDesc('published_at')->paginate(20);

        $newsItems = collect($news->items());

        $newsItems = $newsItems->map(function ($news) {
            $news->published = $news->published_at->format('Y-m-d H:i');

            $_category = [];

            foreach($news->category as $category) {
                $_category[] = $category->name;
            }

            $news->category_name = implode(', ', $_category);

            return $news;
        });

        return view('news.index',compact('news','newsItems'));
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return \Illuminate\Contracts\View\View
     */
    public function create() : View {
        $this->authorize('news_create');

        $categories = Category::whereShow(TRUE)->get();

        return view('news.create', compact('categories'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) : RedirectResponse {
        $this->authorize('news_create');

        if(mb_strlen($request->get('name')) > 108) {
            return back()->with('error','Name must be less than 108 symbols');
        }

        $request->merge([
            'content' => preg_replace('/\"([^<>]*?)\"(?=(?:[^>]*?(?:<|$)))/u', "&laquo;\\1&raquo;", htmlspecialchars_decode($request->get('content')))
        ]);

        $request->validate([
            'name'         => 'required|string',
            'category'     => 'required|array',
            'category.*'   => 'required|numeric|exists:categories,id',
            'published_at' => 'required|date|date_format:Y-m-d\TH:i',
            'title'        => 'required|string|max:255',
            'description'  => 'required|string|max:255',
            'image'        => 'required|image|max:2048|mimes:jpg,jpeg,png',
            'show'         => 'sometimes|accepted',
            'show_img'     => 'sometimes|accepted',
            'show_user'    => 'sometimes|accepted',
            'on_index'     => 'sometimes|accepted',
            'top_left'     => 'sometimes|accepted',
            'top_right'    => 'sometimes|accepted',
            'content'      => 'required|string',
            'gallery' => 'sometimes|required|array',
            'gallery.*' => 'required_with:gallery|string',
            'gallery_removed' => 'sometimes|required|array',
            'gallery_removed.*' => 'required_with:gallery|string',
        ]);

        $news = News::create([
            'user_id'      => auth()->user()->id,
            'show'         => $request->boolean('show'),
            'show_img'     => $request->boolean('show_img'),
            'show_user'    => $request->boolean('show_user'),
            'on_index'     => $request->boolean('on_index'),
            'top_left'     => $request->boolean('top_left'),
            'top_left_at'  => now(),
            'top_right'    => $request->boolean('top_right'),
            'top_right_at' => now(),
            'name'         => $request->input('name'),
            'title'        => $request->input('description'),
            'description'  => $request->input('description'),
            'image'        => $this->uploadAsset($request->file('image')),
            'content'      => htmlentities($request->input('content')),
            'published_at' => $request->get('published_at'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if($request->boolean('show')) {
            foreach(User::all() as $user) {
                $user->notify(new NewsPublished([
                    'name' => $news->name
                ]));
            }
        }else {
            foreach(User::all() as $user) {
                $user->notify(new NewsAdded([
                    'name' => $news->name
                ]));
            }
        }

        foreach($request->get('category') as $categoryId) {
            $news->category()->attach($categoryId);
        }

        foreach($request->input('gallery',[]) as $filename){
            $news->gallery()->create([
               'filename' => $filename
            ]);
        }

        foreach($request->input('gallery_removed',[]) as $filename){
            $news->gallery()->where('filename','=',str_replace('https://caliber.az/storage/','',$filename))->delete();
        }

        cache()->tags('news')->flush();

        if($news->published_at > now()) {
            dispatch(new UpdateCache($news->id))->delay($news->published_at);
        }else{
            $this->exec("curl -I https://caliber.az -H \"cachepurge: true\"");
            foreach($news->category as $category) {
                $this->exec("curl -I https://caliber.az/category/{$category->slug} -H \"cachepurge: true\"");
            }
            sleep(1);
        }

        return redirect()->back()->with('success', 'News post added successfully');
    }

    /**
     * @param int $news
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(int $news) : View {
        $this->authorize('news_update');

        $news = News::withTrashed()->findOrFail($news);

        $categories = Category::whereShow(TRUE)->get();

        return view('news.edit', compact('news', 'categories'));
    }

    private function generateSlug(string $slug, bool $clearLast = FALSE) {
        $slug = Str::slug($slug);

        $count = News::withTrashed()->whereSlug($slug)->count();

        if($count > 0) {
            $slug .= "-" . $count;

            return $this->generateSlug($slug);
        }

        return $slug;
    }

    /**
     * @param \App\Models\News         $news
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(int $news, Request $request) : RedirectResponse {
        $this->authorize('news_update');

        if(mb_strlen($request->get('name')) > 108) {
            return back()->with('error','Name must be less than 108 symbols');
        }

        $news = News::withTrashed()->findOrFail($news);

        $request->merge([
            'content' => preg_replace('/\"([^<>]*?)\"(?=(?:[^>]*?(?:<|$)))/u', "&laquo;\\1&raquo;", htmlspecialchars_decode($request->get('content')))
        ]);

        $request->validate([
            'name'         => 'required|string',
            'category'     => 'required|array',
            'category.*'   => 'required|numeric|exists:categories,id',
            'published_at' => 'required|date|date_format:Y-m-d\TH:i',
            'title'        => 'required|string|max:255',
            'description'  => 'required|string|max:255',
            'image'        => 'sometimes|required|image|max:2048|mimes:jpg,jpeg,png',
            'show'         => 'sometimes|accepted',
            'show_img'     => 'sometimes|accepted',
            'show_user'    => 'sometimes|accepted',
            'on_index'     => 'sometimes|accepted',
            'top_left'     => 'sometimes|accepted',
            'top_right'    => 'sometimes|accepted',
            'content'      => 'required|string',
            'gallery' => 'sometimes|required|array',
            'gallery.*' => 'required_with:gallery|string',
            'gallery_removed' => 'sometimes|required|array',
            'gallery_removed.*' => 'required_with:gallery|string',
        ]);

        $data = [
            'name'         => $request->input('name'),
            'show'         => $request->boolean('show'),
            'show_img'     => $request->boolean('show_img'),
            'show_user'    => $request->boolean('show_user'),
            'on_index'     => $request->boolean('on_index'),
            'slug'         => Str::slug($request->input('name')) . '-' . $news->id,
            'title'        => $request->input('description'),
            'description'  => $request->input('description'),
            'content'      => htmlentities($request->input('content')),
            'published_at' => $request->get('published_at'),
            'deleted_at'   => NULL,
            'updated_at' => now(),
        ];

        $news->category()->detach();

        foreach($request['category'] as $category) {
            $news->category()->attach($category);
        }

        if($request->has('image')) {
            $data['image'] = $this->uploadAsset($request->file('image'), $news->image);
        }

        if($request->boolean('show')) {
            $data['top_left']     = $request->boolean('top_left');
            $data['top_left_at']  = now();
            $data['top_right']    = $request->boolean('top_right');
            $data['top_right_at'] = now();
        }else {
            $data['top_left']  = FALSE;
            $data['top_right'] = FALSE;
        }

        $news->update($data);

        if($request->boolean('show')) {
            foreach(User::all() as $user) {
                $user->notify(new NewsPublished([
                    'name' => $news->name
                ]));
            }
        }

        foreach($request->input('gallery',[]) as $filename){
            $news->gallery()->create([
                'filename' => $filename
            ]);
        }

        foreach($request->input('gallery_removed',[]) as $filename){
            $news->gallery()->where('filename','=',str_replace('https://caliber.az/storage/','',$filename))->delete();
        }

        cache()->tags('news')->flush();

        if($news->published_at > now()) {
            dispatch(new UpdateCache($news->id))->delay($news->published_at);
        }else{
            sleep(1);
            $this->exec("curl -I https://caliber.az -H \"cachepurge: true\"");
            foreach($news->category as $category) {
                $this->exec("curl -I https://caliber.az/category/{$category->slug} -H \"cachepurge: true\"");
            }
            $this->exec("curl -I https://caliber.az/post/{$news->slug} -H \"cachepurge: true\"");
        }

        return redirect()->back()->with('success', 'News post updated successfully');
    }

    /**
     * @param \App\Models\News $news
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(News $news) {
        $this->authorize('news_delete');

        $slug = $news->slug;

        $categories = $news->category->toArray();

        $news->delete();
        cache()->tags('news')->flush();

        $this->exec("curl -I https://caliber.az -H \"cachepurge: true\"");
        foreach($categories as $category) {
            $this->exec("curl -I https://caliber.az/category/{$category['slug']} -H \"cachepurge: true\"");
        }
        $this->exec("curl -I https://caliber.az/post/{$slug} -H \"cachepurge: true\"");
sleep(2);
        return redirect()->route('news.index')->with('success', 'News post soft deleted successfully');
    }

    /**
     * @param \App\Models\Category     $category
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function apiPosts(Category $category, Request $request) : Response {
        $offset = $request->query('limit') * ( $request->query('page') - 1 );
        $limit  = $request->get('limit');

        $posts = $category->news()
                          ->whereNull('deleted_at')
                          ->whereShow(TRUE)
                          ->where('published_at', '<=', now()->format('Y-m-d H:i:s'))
                          ->orderByDesc('published_at')
                          ->setEagerLoads([])
                          ->whereHas('user')
                          ->with('category:id,name,slug');

        $total = $posts->count();

        $posts = $posts->limit($limit)->offset($offset)->get()->map(function (News $post) {
            $post->date = Carbon::parse($post->published_at)->translatedFormat('d M Y, H:i');
            $post->name = Str::limit($post->name, 108);

            return $post->only([
                'slug',
                'image',
                'name',
                'category',
                'date',
            ]);
        });

        return response([
            'result' => $posts,
            'pages_left' => $total > $limit || $total == $limit,
        ]);
    }

    /**
     * @param string                   $news
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function apiPost(string $news, Request $request) : Response {
        $news = News::with('category:id,name,slug')
                    ->with('user:id,name,username')
                    ->whereHas('user')
                    ->whereHas('category')
                    ->whereSlug($news)
                    ->get()
                    ->first();

        if(!$news) {
            abort(404);
        }

        $data = $news->only([
            'category',
            'name',
            'content',
            'title',
            'description',
            'image',
            'view',
            'user',
            'show_img',
            'show_user'
        ]);

        $data['category_name'] = '';

        foreach($data['category'] as $category) {
            $data['category_name'] .= '#' . mb_strtoupper($category['name']) . ' ';
        }

        $data['date']     = Carbon::parse($news->published_at)->translatedFormat('d M Y, H:i');
        $data['keywords'] = explode(' ', $news->name);
        $data['view']     = $data['view'] == 0 ? 1 : $data['view'];
        $data['gallery'] = $news->gallery->pluck('url');

        return response($data);
    }

    public function apiPostView(string $news, Request $request) : Response {
        $news = News::setEagerLoads([])->whereSlug($news)
                    ->get()
                    ->first();

        if(!$news) {
            abort(404);
        }

        $news->update([
            'view' => $news->view +1
        ]);

        return response([
           'view' => $news->view +1
        ]);
    }

    /**
     * @param \App\Models\Category     $category
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function apiPostRelated(string $news, Request $request) : Response {
        $news = News::with('category:id,name,slug')->with('user:id,name,username')->whereSlug($news)->get()->first();

        if(!$news) {
            abort(404);
        }

        $offset = $request->query('limit') * ( $request->query('page') - 1 );
        $limit  = $request->get('limit');

        $current = $news->id;

        $data = $news->category()
                     ->first()
                     ->news()
                     ->whereHas('user')
                     ->where('published_at', '<=', now()->format('Y-m-d H:i:s'))
                     ->where('news.id', '!=', $current)
                     ->setEagerLoads([])
                     ->with('category:id,name,slug')
                     ->orderByDesc('published_at');

        $total = $data->count();

        $data = cache()->tags('news')->rememberForever("$current-related-$limit-$offset",function() use ($limit, $offset,$data) {
            return $data = $data->limit($limit)->offset($offset)->get();
        });

        return response([
            'result'     => $data->map(function (News $post) {
                $post->date = Carbon::parse($post->published_at)->translatedFormat('d M Y, H:i');

                $ctgry = [];

                foreach($post->category as $category) {
                    $ctgry[] = [
                        'name' => '#' . mb_strtoupper($category->name) . ' ',
                        'slug' => $category->slug
                    ];
                }

                $post->ctgry = $ctgry;
                $post->name = Str::limit($post->name, 108);

                return $post->only([
                    'slug',
                    'image',
                    'name',
                    'ctgry',
                    'date',
                ]);
            }),
            'pages_left' => $total > $limit || $total == $limit,
        ]);
    }

    public function apiTopPosts() : Response {
        $news = cache()->tags('news')->rememberForever('top',function() {
            $news = [];

            $postLeft = News::whereShow(TRUE)
                        ->whereHas('user')
                        ->where('published_at', '<=', now()->format('Y-m-d H:i:s'))
                        ->whereTopLeft(TRUE)
                        ->orderByDesc('top_left_at')
                        ->setEagerLoads([])
                        ->with('category:id,name,slug')
                        ->get()
                        ->map(function (News $post) {
                            $post->date = Carbon::parse($post->published_at)->translatedFormat('d M Y, H:i');

                            $ctgry = [];

                            foreach($post->category as $category) {
                                $ctgry[] = [
                                    'name' => '#' . mb_strtoupper($category->name) . ' ',
                                    'slug' => $category->slug
                                ];
                            }
                            $post->ctgry = $ctgry;

                            $post->name = Str::limit($post->name, 80);

                            return $post->only([
                                'slug',
                                'image',
                                'name',
                                'ctgry',
                                'date',
                            ]);
                        });

            if($postLeft->count() > 0) {
                $news['left1'] = $postLeft->first();
                $news['left2'] = $postLeft->skip(1)->take(1)->first();
                $news['left3'] = $postLeft->skip(2)->take(1)->first();
            }

            $postRight = News::whereShow(TRUE)
                        ->whereHas('user')
                        ->where('published_at', '<=', now()->format('Y-m-d H:i:s'))
                        ->whereTopRight(TRUE)
                        ->orderByDesc('top_right_at')
                        ->setEagerLoads([])
                        ->with('category:id,name,slug')
                        ->get()
                        ->map(function (News $post) {
                            $post->date = Carbon::parse($post->published_at)->translatedFormat('d M Y, H:i');

                            $ctgry = [];

                            foreach($post->category as $category) {
                                $ctgry[] = [
                                    'name' => '#' . mb_strtoupper($category->name) . ' ',
                                    'slug' => $category->slug
                                ];
                            }
                            $post->ctgry = $ctgry;

                            $post->name = Str::limit($post->name, 80);
                            return $post->only([
                                'slug',
                                'image',
                                'name',
                                'ctgry',
                                'date',
                            ]);
                        });

            if($postRight->count() > 0) {
                $news['right1'] = $postRight->first();
                $news['right2'] = $postRight->skip(1)->take(1)->first();
            }

            return $news;
        });

        return response($news);
    }

    public function apiRecentPosts(Request $request) : Response {
        $offset = $request->query('limit') * ( $request->query('page') - 1 );
        $limit  = $request->get('limit');

        $j = 1;

        $news = News::whereShow(TRUE)
                    ->whereHas('user')
                    ->where('published_at', '<=', now()->format('Y-m-d H:i:s'))
                    ->limit($limit)
                    ->offset($offset)
                    ->orderByDesc('published_at')
                    ->setEagerLoads([]);

        $ids = [];

        $ids[] =  News::whereShow(TRUE)
                                  ->whereHas('user')
                                  ->where('published_at', '<=', now()->format('Y-m-d H:i:s'))
                                  ->whereTopLeft(TRUE)
                                  ->orderByDesc('top_left_at')
                                  ->setEagerLoads([])
                                  ->with('category:id,name,slug')
                                  ->get()
                                  ->map(function (News $post) {
                                      $post->date = Carbon::parse($post->published_at)->translatedFormat('d M Y, H:i');

                                      $ctgry = [];

                                      foreach($post->category as $category) {
                                          $ctgry[] = [
                                              'name' => '#' . mb_strtoupper($category->name) . ' ',
                                              'slug' => $category->slug
                                          ];
                                      }
                                      $post->ctgry = $ctgry;

                                      $post->name = Str::limit($post->name, 80);

                                      return $post->only([
                                          'slug',
                                          'image',
                                          'name',
                                          'ctgry',
                                          'date',
                                          'id',
                                      ]);
                                  })->first()['id'];

        $ids[] = News::whereShow(TRUE)
                                  ->whereHas('user')
                                  ->where('published_at', '<=', now()->format('Y-m-d H:i:s'))
                                  ->whereTopRight(TRUE)
                                  ->orderByDesc('top_right_at')
                                  ->setEagerLoads([])
                                  ->with('category:id,name,slug')
                                  ->get()
                                  ->map(function (News $post) {
                                      $post->date = Carbon::parse($post->published_at)->translatedFormat('d M Y, H:i');

                                      $ctgry = [];

                                      foreach($post->category as $category) {
                                          $ctgry[] = [
                                              'name' => '#' . mb_strtoupper($category->name) . ' ',
                                              'slug' => $category->slug
                                          ];
                                      }
                                      $post->ctgry = $ctgry;

                                      $post->name = Str::limit($post->name, 80);
                                      return $post->only([
                                          'slug',
                                          'image',
                                          'name',
                                          'ctgry',
                                          'date',
                                          'id',
                                      ]);
                                  })->first()['id'];

        if($request->query('exclude') === 'true') {
            $news->whereNotIn('id', $ids);
        }

        if(!$request->boolean('bar')) {
            $news->whereOnIndex(TRUE);
        }

        $total = with(clone $news)->whereHas('category')->whereHas('user')->with('category:id,name,slug')->count();

        $news = cache()->tags(['news'])->rememberForever("recent_$offset-$limit", function() use($news) {
            $news = $news->whereHas('category')->whereHas('user')->with('category:id,name,slug')->get();

            return $news;
        });

        $news = $news->map(function (News $post) use ($request, &$j) {
            $fields = [
                'slug',
                'name',
                'date',
                'ctgry',
                'row',
                'short_date',
            ];

            if($request->query('short') === 'true') {
                $post->date = Carbon::parse($post->published_at)->translatedFormat('H:i');
            }else {
                $post->date = Carbon::parse($post->published_at)->translatedFormat('d M Y, H:i');
                $fields[]   = 'image';
            }

            $post->name = Str::limit($post->name, 108);

            $post->short_date = Carbon::parse($post->published_at)->format('d.m.Y');

            if($j < 4) {
                $post->row = 3;
            }else {
                if($j < 6) {
                    $post->row = 2;
                }else {
                    if($j % 5 === 0 || $j % 5 === 4) {
                        //                            $post->row = 2;
                        $post->row = 3;
                    }else {
                        $post->row = 3;
                    }
                }
            }

            $ctgry = [];

            foreach($post->category as $category) {
                $ctgry[] = [
                    'name' => '#' . mb_strtoupper($category->name) . ' ',
                    'slug' => $category->slug
                ];
            }
            $post->ctgry = $ctgry;

            $j++;

            return $post->only($fields);
        });

        if($request->boolean('bar')) {
            $news = $news->groupBy('short_date');
        }


        return response([
            'result' => $news->toArray(),
            'pages_left' => $total > $limit || $total == $limit,
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function apiSearch(Request $request) : Response {
        $request->validate([
            'keyword' => 'required|string',
        ]);

        $keyword = urldecode(htmlspecialchars($request->get('keyword')));

        $offset = $request->query('limit') * ( $request->query('page') - 1 );
        $limit  = $request->get('limit');

        $posts = News::where('published_at', '<=', now()->format('Y-m-d H:i:s'))->with('category:id,name,slug')->with('user:id,name');

        $keywords = explode(' ', $keyword);

        $posts->where(function ($q) use ($keywords) {
            $q->where('name', 'ilike', "%$keywords[0]%")
              ->orWhere('title', 'ilike', "%$keywords[0]%")
              ->orWhere('description', 'ilike', "%$keywords[0]%")
              ->orWhere('content', 'ilike', "%$keywords[0]%");

            foreach($keywords as $index => $word) {
                if($index === 0) {
                    continue;
                }

                $q->orWhere('name', 'ilike', "%$word%")
                  ->orWhere('title', 'ilike', "%$word%")
                  ->orWhere('description', 'ilike', "%$word%")
                  ->orWhere('content', 'ilike', "%$word%");
            }
        });

        $posts = $posts->latest('published_at')
                       ->whereHas('user')
                       ->whereHas('category')
                       ->limit($limit)
                       ->offset($offset)
                       ->whereShow(TRUE)
                       ->get()
                       ->unique('id');

        $total = $posts->count();

        return response([
            'result'     => $posts->map(function (News $post) {
                $post->date = Carbon::parse($post->published_at)->translatedFormat('d M Y, l');
                $post->desc = Str::limit(html_entity_decode(strip_tags($post->content)), 200);

                $ctgry = [];

                foreach($post->category as $category) {
                    $ctgry[] = [
                        'name' => '#' . mb_strtoupper($category->name) . ' ',
                        'slug' => $category->slug
                    ];
                }
                $post->ctgry = [];

                $post->name = Str::limit($post->name, 108);

                return $post->only([
                    'slug',
                    'image',
                    'name',
                    'ctgry',
                    'date',
                    'desc',
                    'published_at'
                ]);
            })->values(),
            'pages_left' => round($total / $limit) > ( $request->query('page') - 1 )
        ]);
    }

    public function restore(int $news) {
        $news = News::withTrashed()->findOrFail($news);

        $news->restore();

        $news->show     = FALSE;
        $news->on_index = FALSE;
        $news->save();

        sleep(1);
        $this->exec("curl -I https://caliber.az -H \"cachepurge: true\"");
        foreach($news->category as $category) {
            $this->exec("curl -I https://caliber.az/category/{$category->slug} -H \"cachepurge: true\"");
        }
        $this->exec("curl -I https://caliber.az/post/{$news->slug} -H \"cachepurge: true\"");

        return back()->with('success', 'новости были восстановлены');
    }

    public function ready(News $news) {
        $news->ready = !$news->ready;

        $news->save();
        sleep(1);
        $this->exec("curl -I https://caliber.az -H \"cachepurge: true\"");
        foreach($news->category as $category) {
            $this->exec("curl -I https://caliber.az/category/{$category->slug} -H \"cachepurge: true\"");
        }
        $this->exec("curl -I https://caliber.az/post/{$news->slug} -H \"cachepurge: true\"");

        return back()->with('success', 'Done!');
    }

    public function unlock(News $news) {
        $news->is_editing = FALSE;
        $news->save();
        sleep(1);
        $this->exec("curl -I https://caliber.az -H \"cachepurge: true\"");
        foreach($news->category as $category) {
            $this->exec("curl -I https://caliber.az/category/{$category->slug} -H \"cachepurge: true\"");
        }
        $this->exec("curl -I https://caliber.az/post/{$news->slug} -H \"cachepurge: true\"");

        exit;
    }

}
