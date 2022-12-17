<?php

namespace Tests\Unit\Billing;

use App\Billing\StripePaymentGateway;
use App\Billing\PaymentFailedException;
use Stripe\StripeClient;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));
        $this->lastCharge = $this->paymentGateway->lastCharge();
    }

    /** @test */
    function charges_with_a_valid_payment_token_are_successful()
    {
        $this->paymentGateway->charge(2500, $this->paymentGateway->validToken());

        $this->assertCount(1, $this->paymentGateway->chargesBefore($this->lastCharge));
        $this->assertEquals(2500, $this->paymentGateway->lastCharge()->amount);
    }

}
