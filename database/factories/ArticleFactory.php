<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->title;

        return [
            'title' => $title,
            'subtitle' => $this->faker->word,
            'content' => $this->faker->text,
            'link' => $this->faker->url,
            'slug' => Str::slug($title)
        ];
    }
}
