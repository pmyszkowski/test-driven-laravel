<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReservationTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function calculating_the_total_cost()
    {
        $concert = Concert::factory()->create(['ticket_price' => 1200])->addTickets(3);
        $tickets = $concert->findTickets(3);

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
    }

}
