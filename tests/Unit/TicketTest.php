<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;


class TicketTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_ticket_can_be_released()
    {
        /** @var Concert $concert */
        $ticket = Ticket::factory()->create();
        $this->assertNull($ticket->reserved_at);

        $ticket->reserve();

        $this->assertNotNull($ticket->fresh()->reserved_at);
    }

/** @test */
    public function a_ticket_can_be_reserved()
    {
        /** @var Concert $concert */
        $concert = Concert::factory()->create();
        $concert->addTickets(1);
        $order = $concert->orderTickets('jane@example.com', 1);
        /** @var Ticket $ticket */
        $ticket = $order->tickets()->first();

        $this->assertEquals($order->id, $ticket->order_id);

        $ticket->release();

        $this->assertNull($ticket->fresh()->order_id);
    }


}
