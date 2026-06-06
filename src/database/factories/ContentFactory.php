<?php

namespace Database\Factories;

use App\Models\Content;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ContentFactory extends Factory
{
    protected $model = Content::class;

    public function definition()
    {
        $title = $this->faker->unique()->words(4, true);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'format' => $this->faker->randomElement(['text', 'video', 'system', 'external_tool', 'image']),
            'description' => $this->faker->realText(420),
            'price' => $this->faker->randomElement([0, 500, 980, 1500, 2400, 3980, 6500]),
            'thumbnail_path' => 'assets/content-placeholder.svg',
            'license_type' => $this->faker->randomElement(['個人利用可', '個人利用・商用利用可', '教材利用可']),
            'environment' => $this->faker->randomElement(['Windows / macOS', 'Excel 2021以降', 'Laravel 8 / Docker', 'Notion', 'ブラウザ']),
            'file_size_mb' => $this->faker->randomFloat(2, 1, 480),
            'rating_rate' => $this->faker->numberBetween(86, 100),
            'ratings_count' => $this->faker->numberBetween(4, 240),
            'profile_order' => $this->faker->numberBetween(1, 40),
            'status' => 'published',
            'published_at' => $this->faker->dateTimeBetween('-5 months', 'now'),
        ];
    }
}
