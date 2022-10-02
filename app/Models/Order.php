<?php

namespace App\Models;

use App\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = array();

    static public function forTickets($tickets, $email, $amount) {

        /** @var Order $order */
        $order = self::create( array(
            'email' => $email,
            'amount' => $amount,
        ) );

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    public static function fromReservation(Reservation $reservation)
    {
        /** @var Order $order */
        $order = self::create( array(
            'email' => $reservation->email(),
            'amount' => $reservation->totalCost(),
        ) );

        $order->tickets()->saveMany($reservation->tickets());

        return $order;
    }

    public function concert() {

        return $this->belongsTo(Concert::class );
    }

    public function tickets() {

        return $this->hasMany( Ticket::class );
    }

    public function toArray()
    {
        return [
            'email' => $this->email,
            'ticket_quantity' => $this->ticketQuantity(),
            'amount' => $this->amount,
//            'amount' => $this->ticketQuantity() * $this->concert->ticket_price,
        ];
    }

    public function ticketQuantity()
    {
        return $this->tickets()->count();
    }

}
