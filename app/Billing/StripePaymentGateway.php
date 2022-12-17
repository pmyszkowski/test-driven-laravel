<?php

namespace App\Billing;

use Stripe\StripeClient;

class StripePaymentGateway implements PaymentGateway
{

    private StripeClient $stripe;

    public function __construct($apiKey) {

        $this->stripe  = new StripeClient($apiKey);
    }

    public function validToken()
    {
        $token = $this->stripe->tokens->create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => (int) date('Y') + 1,
                'cvc' => '123',
            ],
        ])->id;

        return $token;
    }

    public function charge($amount, $token)
    {
        $this->stripe->charges->create([
            'amount' => $amount,
            'source' => $token,
            'currency' => 'usd',
        ]);

        $this->charges[] = $amount;
    }

    public function lastChargeBefore($endingBefore)
    {
        return $this->lastCharge($endingBefore);
    }

    /**
     * @param $endingBefore
     * @return mixed
     */
    public function lastCharge($endingBefore = null)
    {
        if (isset($endingBefore))
            $lastCharge = $this->chargesBefore($endingBefore, 1);
        else
            $lastCharge = $this->charges(1);

        return $lastCharge[0];
    }

    public function chargesBefore($endingBefore, $limit = 10)
    {
        $params = [
            'limit' => $limit,
            'ending_before' => $endingBefore->id,
        ];

        return $this->stripe->charges->all($params)['data'];
    }

    public function charges($limit = 10)
    {
        $params = [
            'limit' => $limit,
        ];

        return $this->stripe->charges->all($params)['data'];
    }

}
