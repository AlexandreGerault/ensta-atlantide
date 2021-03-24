<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_be_redirected_to_home_if_is_a_guest()
    {
        $response = $this->get(route('message.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_it_display_list_of_logged_in_user_messages()
    {
        /**
         * @var User $user
         */
        $user = User::factory()->has(Message::factory(), 'messages')->create();
        $this->actingAs($user);

        $response = $this->get(route('message.index'));

        $response->assertOk();
        $user->messages()->each(
            function (Message $message) use ($response) {
                $response->assertSee(Message::CATEGORIES[$message->category]);
                $response->assertSee($message->subject);
            }
        );
    }

    public function test_a_user_can_see_a_message_it_owns()
    {
        /**
         * @var User $user
         */
        $user = User::factory()->has(Message::factory(), 'messages')->create();
        $this->actingAs($user);
        /**
         * @var Message $message
         */
        $message = $user->messages()->firstOrFail();

        $response = $this->get(route('message.show', compact('message')));

        $response->assertOk();
        $user->messages()->each(
            function (Message $message) use ($response) {
                $response->assertSee($message->content);
            }
        );
    }

    public function test_a_user_cannot_see_message_it_does_not_own()
    {
        /**
         * @var User $user
         */
        $user = User::factory()->has(Message::factory(), 'messages')->create();
        $this->actingAs($user);

        $response = $this->get(route('message.show', ['message' => Message::factory()->create()]));

        $response->assertForbidden();
    }
}
