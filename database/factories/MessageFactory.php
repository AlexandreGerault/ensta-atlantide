<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'subject' => $this->faker->words(4, true),
            'category' => $this->faker->numberBetween(1, 5),
            'content' => $this->faker->text(100),
            'email' => $this->faker->email,
            'sender_ip' => $this->faker->ipv4
        ];
    }
}
