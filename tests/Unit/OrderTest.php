<?php

namespace Tests\Unit;

use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function creating_an_order_from_tickets_email_and_amount()
    {
        /** @var Concert $concert */
        $concert = Concert::factory()->create()->addTickets(5);

        $this->assertEquals( 5, $concert->ticketsRemaining() );

        $order = Order::forTickets( $concert->findTickets(3), 'john@example.com', 3600 );

        $this->assertEquals('john@example.com', $order->email );
        $this->assertEquals( 3, $order->ticketQuantity() );
        $this->assertEquals( 3600, $order->amount );
        $this->assertEquals( 2, $concert->ticketsRemaining() );
    }

    /** @test */
    public function converting_to_an_array()
    {
        /** @var Concert $concert */
        $concert = Concert::factory()->create(['ticket_price' => 1200])->addTickets(10);

        $order = $concert->orderTickets('jane@example.com', 5);

        $result = $order->toArray();

        $this->assertEquals([
            'email' => 'jane@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000,
        ], $result);
    }

    /** @test */
    public function tickets_are_released_when_an_order_is_canceled()
    {
        /** @var Concert $concert */
        $concert = Concert::factory()->create()->addTickets(10);

        $order = $concert->orderTickets('jane@example.com', 5);

        $this->assertEquals(5, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(10, $concert->ticketsRemaining());
        $this->assertNull(Order::find($order->id));
    }


}
