<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Http\Controllers\Controller;
use App\Models\Concert;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PurchaseTicketsTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    function customer_can_purchase_concert_tickets()
    {

        $paymentGateway = new FakePaymentGateway();
        $this->app->instance( PaymentGateway::class, $paymentGateway );

        // Create a concert
        $concert = Concert::factory()->create( array( 'ticket_price' => 3250 ) );

        // Purchase concert tickets
        $response = $this->postJson( "/concerts/{$concert->id}/orders", array(
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $paymentGateway->getValidTestToken(),
        ) );

        // Assert
        $response->assertStatus(201);

        // Make sure the customer was charged the correct amount
        $this->assertEquals( 9750, $paymentGateway->totalCharges() );

        // Make sure that an order exists for this customer
        $order = $concert->orders()->where( 'email', 'john@example.com' )->first();
        $this->assertNotNull( $order );
        $this->assertEquals( 3, $order->tickets()->count() );
    }

}
