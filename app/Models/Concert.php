<?php

/**
 * @property Order[] $orders
 */

namespace App\Models;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concert extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $dates = ['date'];

    public function getFormattedDateAttribute() {
        return $this->date->format('F j, Y');
    }

    public function getFormattedStartTimeAttribute() {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollarsAttribute() {
        return number_format($this->ticket_price / 100, 2);
    }

    public function scopePublished($query) {
        return $query->whereNotNull('published_at');
    }

    /**
     * @return HasMany
     */
    public function orders() {
        return $this->hasMany( Order::class );
    }
    /**
     * @return HasMany
     */
    public function tickets() {
        return $this->hasMany( Ticket::class );
    }

    /**
     * @param $email
     * @param $ticketsQuantity
     * @return Order
     */
    public function orderTickets( $email, $ticketsQuantity ): Order
    {
        $tickets = $this->tickets()->available()->take($ticketsQuantity)->get();

        if ($tickets->count() !== $ticketsQuantity) {
            throw new NotEnoughTicketsException();
        }

        /** @var Order $order */
        $order = $this->orders()->create( array( 'email' => $email ) );

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    public function addTickets($quantity)
    {
        for( $i = 0; $i < $quantity; $i++ ) {

            $this->tickets()->create( array() );
        }

        return $this;
    }

    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }

    public function hasOrderFor(string $customerEmail)
    {

        return $this->orders()->where('email', $customerEmail)->exists();
    }

    public function ordersFor(string $customerEmail)
    {

        return $this->orders()->where('email', $customerEmail)->get();
    }

}
