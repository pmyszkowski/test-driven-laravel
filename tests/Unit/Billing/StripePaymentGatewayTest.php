<?php

namespace Tests\Unit\Billing;

use App\Billing\StripePaymentGateway;
use App\Billing\PaymentFailedException;
use Stripe\StripeClient;
use Tests\TestCase;

/**
 * @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    /**
     * @var mixed Last charge that happen before any of the tests starts.
     */
    private mixed $lastCharge;

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

    /**
     * @test
     */
    function charges_with_an_invalid_payment_token_fail()
    {
        try {
            $this->paymentGateway->charge( 2500, 'invalid-payment-token' );
        } catch(PaymentFailedException) {
            $this->assertCount(0, $this->paymentGateway->chargesBefore($this->lastCharge));
            return;
        }

        $this->fail('Charging with an invalid payment token did not throw a PaymentFailedException.');
    }

}
