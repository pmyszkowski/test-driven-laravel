<?php

namespace Tests\Feature;

use App\Models\Concert;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ViewConcertListingTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    function user_can_view_a_concert_listing()
    {
        // Arrange
        // Create a concert
        $concert = Concert::create([
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
        ]);

        // Act
        // View the concert listing
        $request = $this->get('/concerts/'.$concert->id);

        // Assert
        // See the concert details
        $request->assertOk();

        $request->assertSee('The Red Chord');
        $request->assertSee('with Animosity and Lethargy');
        $request->assertSee('December 13, 2016');
        $request->assertSee('8:00pm');
        $request->assertSee('32.50');
        $request->assertSee('The Mosh Pit');
        $request->assertSee('123 Example Lane');
        $request->assertSee('Laraville, ON 17916');
        $request->assertSee('For tickets, call (555) 555-5555.');
    }
}
