<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Models\Ticket;
use App\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Tests\TestCase;

class ReservationTest extends TestCase
{

    /** @test */
    public function calculating_the_total_cost()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'john@example.com');

        $this->assertEquals(3600, $reservation->totalCost());
    }

    /** @test */
    public function reserved_tickets_are_released_when_a_reservation_is_cancelled()
    {
        $tickets = collect([
//            Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock(),
//            Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock(),
//            Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock(),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
        ]);

        $reservation = new Reservation($tickets, 'john@example.com');

        $reservation->cancel();

        foreach ($tickets as $ticket) {
            $ticket->shouldHaveReceived('release');
        }
    }

    /** @test */
    public function retrieving_the_reservations_tickets()
    {
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets, 'john@example.com');

        $this->assertEquals($tickets, $reservation->tickets());
    }

    /** @test */
    public function retrieving_the_customer_email()
    {
        $reservation = new Reservation(collect(), 'john@example.com');

        $this->assertEquals('john@example.com', $reservation->email() );
    }

}
