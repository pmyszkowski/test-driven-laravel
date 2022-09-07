<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
use App\Models\Order;
use App\Reservation;
use Illuminate\Http\Request;

class ConcertOrdersController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway) {

        $this->paymentGateway = $paymentGateway;

    }

    public function store( $concertId  )
    {
        /** @var Concert $concert */
        $concert = Concert::published()->findOrFail($concertId);

        $this->validate( request(), array(
            'email' => array( 'required', 'email' ),
            'ticket_quantity' => array( 'required', 'integer', 'min:1' ),
            'payment_token' => array( 'required' ),
        ) );

        try {

            // find tickets
            // charge the customer
            // create order for the tickets

            $tickets = $concert->findTickets( request( 'ticket_quantity' ) );

            $reservation = new Reservation($tickets);

            $this->paymentGateway->charge( $reservation->totalCost(), request('payment_token') );

            // PendingOrder
            // InProgressOrder
            // Reservation
//            $this->paymentGateway->charge( $reservation->totalCost(), request('payment_token') );

            $order = Order::forTickets($tickets, request( 'email' ), $reservation->totalCost());

            return response()->json( $order,201 );
        }
        catch (PaymentFailedException $e) {

            return response()->json( [],422 );
        }
        catch (NotEnoughTicketsException $e) {

            return response()->json( [],422 );
        }
    }

}
