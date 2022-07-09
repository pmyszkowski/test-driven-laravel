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

    private function orderTickets($concert, $params) {

        $request = $this->postJson("/concerts/{$concert->id}/orders", $params);

        return $request;
    }

    /** @test */
    function customer_can_purchase_tickets_to_a_published_concert()
    {
        // Create a concert
        $concert = Concert::factory()->published()->create(array('ticket_price' => 3250));

        // Purchase concert tickets
        $response = $this->orderTickets($concert, array(
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
    function cannot_purchase_tickets_to_unpublished_concert()
    {
//        $this->withoutExceptionHandling();

        $concert = Concert::factory()->published()->unpublished()->create();

        $request = $this->orderTickets($concert, array(
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ));

        $request->assertStatus(404);
        $this->assertEquals(0, $concert->orders()->count());
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

        /** @test */
    function an_order_is_not_created_if_payment_fails()
    {
        /** @var Concert $concert */
        $concert = Concert::factory()->published()->create(array('ticket_price' => 3250));

        $request = $this->orderTickets($concert, array(
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-payment-token',
        ));

        $request->assertStatus(422);
        $order = $concert->orders()->where('email', 'john@example.com')->first();
        $this->assertNull($order);
    }

    /** @test */
    function email_is_required_to_purchase_tickets()
    {
        $concert = Concert::factory()->published()->create();

        // Purchase concert tickets
        $response = $this->orderTickets($concert, array(
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ));

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor( 'email' );
    }

    /** @test */
    function email_must_be_valid_to_purchase_tickets()
    {
        $concert = Concert::factory()->published()->create();

        // Purchase concert tickets
        $response = $this->orderTickets($concert, array(
            'email' => 'some_wrong_email',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ));

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor( 'email' );
    }

    /** @test */
    function ticket_quantity_is_required_to_purchase_tickets()
    {
        $concert = Concert::factory()->published()->create();

        // Purchase concert tickets
        $response = $this->orderTickets($concert, array(
            'email' => 'john@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ));

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor( 'ticket_quantity' );
    }

    /** @test */
    function ticket_quantity_must_be_at_least_1_to_purchase_tickets()
    {
        $concert = Concert::factory()->published()->create();

        // Purchase concert tickets
        $response = $this->orderTickets($concert, array(
            'email' => 'john@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ));

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor( 'ticket_quantity' );
    }

    /** @test */
    function payment_token_is_required_to_purchase_tickets()
    {
        $concert = Concert::factory()->published()->create();

        // Purchase concert tickets
        $response = $this->orderTickets($concert, array(
            'email' => 'john@example.com',
            'ticket_quantity' => 1,
        ));

        $response->assertStatus(422);
        $response->assertJsonValidationErrorFor( 'payment_token' );
    }
}
