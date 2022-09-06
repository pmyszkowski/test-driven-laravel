<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
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
            $this->paymentGateway->charge( request( 'ticket_quantity' ) * $concert->ticket_price, request('payment_token') );
            $order = $concert->createOrder( request( 'email' ), $tickets );

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
