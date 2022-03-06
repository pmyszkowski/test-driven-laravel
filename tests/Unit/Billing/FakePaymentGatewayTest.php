<?php

namespace Billing;

use App\Billing\FakePaymentGateway;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{

    /** @test */
    function charges_with_a_vaid_payment_token_are_successful()
    {

        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge( 2500, $paymentGateway->getValidTestToken() );

        $this->assertEquals( 2500, $paymentGateway->totalCharges() );

    }

}
