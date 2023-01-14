<?php

namespace Database\Seeders;

use App\Models\Concert;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Concert::factory()->published()->create([
             'title' => 'The Red Chord',
             'subtitle' => 'with Animosity and Lethargy',
             'date' => Carbon::parse('December 13, 2016 8:00pm'),
             'ticket_price' => 3250,
             'venue' => 'The Mosh Pit',
             'venue_address' => '123 Example Lane',
             'city' => 'Laraville',
             'state' => 'ON',
             'zip' => '17916',
             'additional_information' => 'For tickets, call (555) 555-5555.',
        ])->addTickets(10);
    }
}
