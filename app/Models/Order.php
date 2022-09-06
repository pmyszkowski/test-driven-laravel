<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = array();

    public function concert() {

        return $this->belongsTo(Concert::class );
    }

    public function tickets() {

        return $this->hasMany( Ticket::class );
    }

    public function cancel() {

        /** @var Ticket $ticket */
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }

        $this->delete();
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
