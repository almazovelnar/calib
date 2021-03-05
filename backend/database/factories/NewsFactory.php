<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class NewsFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = News::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            'show'        => TRUE,
            'name'        => $this->faker->realText(100),
            'slug'        => Str::slug($this->faker->realText(130)).'-'.mt_rand(),
            'title'       => $this->faker->realText(15),
            'description' => $this->faker->realText(60),
            'image'       => $this->faker->imageUrl(),
            'content'     => $this->faker->realText(600),
            'published_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }

}
