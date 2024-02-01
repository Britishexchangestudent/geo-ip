<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\GeoIP;
use Tests\TestCase;

class GeoIpModelTest extends TestCase
{
    use RefreshDatabase;

    public function testCanCreateRecord()
    {
        $data = [
            'ip' => '127.0.0.1',
            'country_code' => 'US',
            'country_name' => 'United States',
            'ip_fetched' => true,
        ];

        $geoIP = GeoIP::create($data);

        $this->assertInstanceOf(GeoIP::class, $geoIP);
        $this->assertDatabaseHas('geo_ips', $data);
    }
}

