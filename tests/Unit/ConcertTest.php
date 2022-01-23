<?php

namespace Tests\Unit;

use App\Models\Concert;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ConcertTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function can_get_formatted_date()
    {
        $concert = Factory::factoryForModel(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 8:00pm'),
        ]);

        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    /** @test */
    public function can_get_formatted_start_time()
    {
        $concert = Factory::factoryForModel(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 17:00:00'),
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }


    /** @test */
    public function can_get_ticket_price_in_dollars()
    {
        $concert = Factory::factoryForModel(Concert::class)->make([
            'ticket_price' => 6750,
        ]);

        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    /** @test */
    public function concerts_with_a_published_at_date_are_published()
    {
        $publishedConcertA  = Factory::factoryForModel(Concert::class)->create([ 'published_at' => Carbon::parse('-1 week') ]);
        $publishedConcertB  = Factory::factoryForModel(Concert::class)->create([ 'published_at' => Carbon::parse('-1 week') ]);
        $unpublishedConcert = Factory::factoryForModel(Concert::class)->create([ 'published_at' => null ]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

}
