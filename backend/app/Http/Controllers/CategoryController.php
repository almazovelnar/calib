<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller {

    /**
     * List of categories
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception
     */
    public function index(Request $request) {
        $this->authorize('category_read');

        if($request->isXmlHttpRequest()) {
            return json_encode([
                'data' => Category::all()->toArray()
            ]);
        }

        return view('category.index');
    }

    /**
     * Create new category form
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return \Illuminate\Contracts\View\View
     */
    public function create() : View {
        $this->authorize('category_create');

        return view('category.create');
    }

    /**
     * Save created category
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) : RedirectResponse {
        $this->authorize('category_create');

        $request->merge([
            'slug' => Str::slug($request->input('slug'))
        ]);

        $request->validate([
            'name'        => 'required|string|max:255',
            'order'        => 'required|numeric|min:0|max:20',
            'slug'        => 'required|string|unique:categories',
            'show'        => 'sometimes|accepted',
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:255',
//            'image'       => 'required|image|max:2048|mimes:jpg,jpeg,png'
        ]);

        $category = new Category();

        $category->show        = $request->boolean('show');
        $category->name        = $request->input('name');
        $category->slug        = $request->input('slug');
        $category->title       = $request->input('title');
        $category->order       = $request->input('order');
        $category->description = $request->input('description');
//        $category->image       = $this->uploadAsset($request->file('image'));

        $category->save();

        cache()->tags('news')->flush();

        $this->exec("curl -I https://caliber.az/category/{$category->slug} -H \"cachepurge: true\"");
        $this->exec("curl -I https://caliber.az -H \"cachepurge: true\"");
        sleep(1);

        return redirect()->route('category.index')->with('success', 'New category added successfully.');
    }

    /**
     * Update category form
     *
     * @param string $category
     *
     * @throws \Exception
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Category $category) {
        $this->authorize('category_update');

        return view('category.edit', compact('category'));
    }

    /**
     * Update category
     *
     * @param \App\Models\Category     $category
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Category $category, Request $request) : RedirectResponse {
        $this->authorize('category_update');

        $request->merge([
            'slug' => Str::slug($request->input('slug'))
        ]);

        $request->validate([
            'name'        => 'required|string|max:255',
            'order'        => 'required|numeric|min:0|max:20',
            'slug'        => ['required', 'string', Rule::unique('categories')->ignoreModel($category)],
            'show'        => 'sometimes|accepted',
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:255',
//            'image'       => 'sometimes|required|image|max:2048|mimes:jpg,jpeg,png'
        ]);

        $category->show        = $request->boolean('show');
        $category->name        = $request->input('name');
        $category->slug        = $request->input('slug');
        $category->title       = $request->input('title');
        $category->order       = $request->input('order');
        $category->description = $request->input('description');

//        if($request->has('image')) {
//            $category->image = $this->uploadAsset($request->file('image'), $category->image);
//        }

        $category->save();
        cache()->tags('news')->flush();
        $this->exec("curl -I https://caliber.az/category/{$category->slug} -H \"cachepurge: true\"");
        $this->exec("curl -I https://caliber.az -H \"cachepurge: true\"");
        sleep(1);

        return redirect()->route('category.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Delete the category
     *
     * @param \App\Models\Category $category
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category) : RedirectResponse {
        $this->authorize('category_delete');

        $category->delete();
        cache()->tags('news')->flush();

        $this->exec("curl -I https://caliber.az/category/{$category->slug} -H \"cachepurge: true\"");
        $this->exec("curl -I https://caliber.az -H \"cachepurge: true\"");
        sleep(1);

        return redirect()->route('category.index')->with('success', 'Category soft deleted successfully.');
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function apiList() : Response {
        return response(Category::whereShow(TRUE)->orderBy('order')->get()->map->only(['name', 'slug']));
    }

    /**
     * @param \App\Models\Category $category
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function apiCategory(Category $category) {
        return response($category->only(['id','name','title','slug','description']));
    }
}
