<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up environnement for tests that need some setup
     *
     * @param bool $withArticle
     *
     * @return User
     */
    private function setupUserWithOrdersAndArticle($withArticle = true): User
    {
        /**
         * @var User $user
         */
        $user = User::factory()->has(Order::factory()->count(3), 'orders')->create();
        $this->actingAs($user);

        if ($withArticle) {
            Article::factory()->for($user)->create(['title' => 'texte_bienvenue']);
        }

        return $user;
    }

    public function test_a_guest_is_redirected_to_login_page()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_page_not_found_if_no_welcome_article()
    {
        $this->setupUserWithOrdersAndArticle(false);

        $response = $this->get(route('dashboard'));

        $response->assertNotFound();
    }

    public function test_a_user_can_see_its_orders()
    {
        $this->setupUserWithOrdersAndArticle();

        $response = $this->get(route('dashboard'));

        $response->assertOk();
    }

    public function test_a_user_can_see_its_fidelity_points()
    {
        $user = $this->setupUserWithOrdersAndArticle();
        $points = $user->totalPoints();

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee($points);
    }
}
