<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder {

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        //        \App\Models\User::factory(20)->create();

        $permissions = [];

        foreach(config('permissions') as $group => $details) {
            foreach($details['actions'] as $action => $isActive) {
                $permissions[$group . '_' . $action] = "1";
            }
        }

        User::create([
            'name'       => 'Samir Mammadhasanov',
            'email'      => 'samhsnv@gmail.com',
            'password'   => Hash::make('secret'),
            'position'   => 'IT / Developer',
            'permission' => $permissions,
        ]);

        $categories = [
            'Карабах',
            'Комментарии',
            'Репортажи',
            'Интервью',
            'Новости'
        ];

        foreach($categories as $index => $category) {
            Category::create([
                'order'       => $index + 1,
                'name'        => $category,
                'show'        => TRUE,
                'slug'        => Str::slug($category),
                'title'       => "$category на Caliberaz",
                'description' => "$category на Caliberaz",
                'image'       => '2021/01/31/5B74E8E6-5877-4137-942F-800DA721750F.jpg',
            ]);
        }

        News::factory(600)->create([
            'user_id' => User::all()->random()->id,
        ]);

        foreach(Category::all() as $category) {
            for($i = 0; $i < 30; $i++) {
                try {
                    $category->news()->attach(News::all()->random());
                }catch (QueryException $e) {
                    continue;
                }
            }
        }
    }

}
