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

    protected function setUp() : void {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance( PaymentGateway::class, $this->paymentGateway );
    }

    /** @test */
    function customer_can_purchase_concert_tickets()
    {
        // Create a concert
        $concert = Concert::factory()->create(array('ticket_price' => 3250));

        // Purchase concert tickets
        $response = $this->postJson("/concerts/{$concert->id}/orders", array(
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ));

        // Assert
        $response->assertStatus(201);

        // Make sure the customer was charged the correct amount
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        // Make sure that an order exists for this customer
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());
    }

    /** @test */
    function email_is_required_to_purchase_tickets()
    {
//        $this->withoutExceptionHandling();

        $concert = Concert::factory()->create();

        // Purchase concert tickets
        $response = $this->postJson("/concerts/{$concert->id}/orders", array(
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ));

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor( 'email' );
    }

}
