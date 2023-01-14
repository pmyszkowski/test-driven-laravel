<?php

namespace App\Billing;

use Stripe\StripeClient;
Use Stripe\Exception\InvalidRequestException;

class StripePaymentGateway implements PaymentGateway
{

    private StripeClient $stripe;
    private array $charges;

    public function __construct($apiKey) {

        $this->stripe  = new StripeClient($apiKey);
    }

    public function validToken(): string
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
        try {
            $this->stripe->charges->create([
                'amount' => $amount,
                'source' => $token,
                'currency' => 'usd',
            ]);
        }
        // by catching Stripe exception and throwing our own exception
        // we are able to isolate the rest of our app from knowing that we are using other library under the hood
        catch (InvalidRequestException $e) {
            throw new PaymentFailedException();
        }

        $this->charges[] = $amount;
    }

    /**
     * @return mixed
     */
    public function lastCharge(): mixed
    {
        return $this->charges(1)[0];
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
