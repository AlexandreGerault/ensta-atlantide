<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Quote;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_articles_on_homepage()
    {
        Quote::factory()
             ->for(User::factory(), 'creator')
             ->count(10)->create();

        $articles = Article::factory()
                           ->for(User::factory(), 'user')
                           ->count(4)->state(['published' => 1])
                           ->create();

        $response = $this->get('/');


        $response->assertOk();
        $articles->each(
            function (Article $article) use ($response) {
                $response->assertSee($article->title);
            }
        );
    }

    public function test_it_works_without_quotes()
    {
        $response = $this->get('/');

        $response->assertOk();
    }
}
