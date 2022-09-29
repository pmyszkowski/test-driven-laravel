<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeAvailable($query)
    {
        return $query->whereNull('order_id')->whereNull('reserved_at');
    }

    public function reserve()
    {
       $this->update(['reserved_at' => Carbon::now()]);
    }

    public function release()
    {
        $this->update(['order_id' => null]);
    }

    public function concert() {

        return $this->belongsTo( Concert::class );
    }

    public function getPriceAttribute() {

        return $this->concert->ticket_price;
    }

}
