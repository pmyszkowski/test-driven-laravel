<?php

namespace App;

use App\Models\Order;
use Illuminate\Support\Collection;

class Reservation
{

    private $tickets;
    private $email;

    function __construct(Collection $tickets, $email) {

        $this->tickets = $tickets;
        $this->email = $email;
    }
    public function totalCost()
    {
        return $this->tickets->sum('price');
    }

    public function tickets()
    {
        return $this->tickets;
    }

    public function email()
    {
        return $this->email;
    }

    public function cancel()
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }

//        $this->tickets->each(function ($ticket) {
//            $ticket->release();
//        });
    }

    public function complete($paymentGateway, $paymentToken)
    {
        $paymentGateway->charge( $this->totalCost(), $paymentToken );

        return Order::forTickets($this->tickets(), $this->email(), $this->totalCost());
    }

}
