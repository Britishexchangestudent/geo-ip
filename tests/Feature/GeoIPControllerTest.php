<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class GeoIPControllerTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


    /** @test */
    public function index_method_returns_success_response()
    {
        // Arrange
        $response = $this->get(route('geoip.index'));

        // Assert
        $response->assertStatus(200);
    }

    public function testIndexMethodSetsSessionVariables()
    {
        // Arrange
        $userIp = '127.0.0.1';
        $geoip = [
            'ip' => '127.0.0.1',
            'country_code' => 'US',
            'country_name' => 'United States',
            'ip_fetched' => true,
        ];
        $oldInput = 'previous_input';

        // Set the session variables
        session(['geoip' => $geoip, 'ipFetched' => true, 'oldInput' => $oldInput]);

        // Act
        $response = $this->get(route('geoip.index'));

        // Assert
        $response->assertViewHas('userIp', $userIp);
        $response->assertViewHas('geoip', $geoip);
        $response->assertViewHas('oldInput', $oldInput);
        $response->assertViewHas('ipFetched', true);
    }

    /** @test */
    public function index_method_passes_user_ip_to_view()
    {
        // Arrange
        $userIp = '127.0.0.1';
        $this->app['request']->setUserResolver(function () use ($userIp) {
            return $userIp;
        });

        // Act
        $response = $this->get(route('geoip.index'));

        // Assert
        $response->assertViewHas('userIp', $userIp);
    }

    public function testCacheIsStored()
    {
        $this->withoutMiddleware(); // Disable all middleware for this test

        // Simulate a request with a specific IP
        $ip = '127.0.0.1';
    
        // Mock the HTTP response for a UK IP
        Http::fake([
            "http://ip2c.org/{$ip}" => Http::response('GB;GB;United Kingdom', 200),
        ]);
    
        // Make a request to the 'query' endpoint
        $response = $this->post(route('geoip.query'), [
            'ip' => $ip 
        ]);

        // Follow redirects
        $response->assertRedirect();

    
        // Assert that the cache contains the expected data for the UK
        $this->assertTrue(Cache::has('userIp'));
        $this->assertEquals($ip, Cache::get('userIp'));
    
        $this->assertTrue(Cache::has('geoip'));
        $this->assertEquals([
            'ip' => $ip,
            'country_code' => 'GB',
            'country_name' => 'United Kingdom',
            'ip_fetched' => true,
        ], Cache::get('geoip'));
    
        $this->assertTrue(Cache::has('ipFetched'));
        $this->assertTrue(Cache::get('ipFetched'));
    }


    public function testFailedResponse()
    {
        $this->withoutMiddleware(); // Disable all middleware for this test

        // Simulate a request with a specific IP
        $ip = '127.0.0.1';

        // Mock the HTTP response for a failed request
        Http::fake([
            "http://ip2c.org/{$ip}" => Http::response('Error response', 500),
        ]);

        // Make a request to the 'query' endpoint
        $response = $this->post(route('geoip.query'), ['ip' => $ip]);

        // Assert that the response is a redirect with a specific HTTP status code
        $response->assertStatus(302); // Asserting the HTTP status code for redirect

        // Assert that the response is a redirect
        $response->assertRedirect(route('geoip.index'));

        // Assert that the session has the expected message
        $this->assertEquals('Failed to fetch GeoIP information.', session('message'));

        // Assert that the session has the old input value
        $this->assertEquals($ip, session('oldInput'));
    }

    public function testRequestValidationFailsForInvalidIP()
    {
        $this->withoutMiddleware(); // Disable all middleware for this test

        // Act
        $response = $this->post(route('geoip.query'), ['ip' => 'invalid_ip']);

        // Assert
        $response->assertRedirect(route('geoip.index'));
        $response->assertSessionHasErrors('ip');
        $this->assertEquals('Invalid IP address format.', session('errors')->first('ip'));
    }

    public function testCacheNotAvailable()
    {
        // Arrange
        Cache::shouldReceive('get')->andReturn(null); // Mock Cache::get to return null

        // Act
        $response = $this->get(route('geoip.index'));

        // Assert
        $response->assertSuccessful();

        // Assuming all are null when cache is not available
        $response->assertViewHas('userIp', null); 
        $response->assertViewHas('geoip', null); 
        $response->assertViewHas('oldInput', null); 
        $response->assertViewHas('ipFetched', false); 
    }

}
