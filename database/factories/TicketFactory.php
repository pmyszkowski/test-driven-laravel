<?php

namespace Database\Factories;

use App\Models\Concert;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'concert_id' => function () {
                return Concert::factory()->create()->id;
            }
        ];
    }

    /**
     * Indicate that the ticket is reserved.
     *
     * @return Factory
     */
    public function reserved()
    {
        return $this->state(function (array $attributes) {
            return [
                'reserved_at' => Carbon::now(),
            ];
        });
    }
}
