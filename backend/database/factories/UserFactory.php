<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class UserFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        $permissions = [];

        foreach(config('permissions') as $group => $details) {
            foreach($details['actions'] as $action => $isActive) {
                $permissions[$group . '_' . $action] = "1";
            }
        }

        return [
            'name'           => $this->faker->name,
            'email'          => $this->faker->unique()->safeEmail,
            'position'       => $this->faker->jobTitle,
            'password'       => \Hash::make('secret'),
            'permission'     => $permissions
        ];
    }

}
