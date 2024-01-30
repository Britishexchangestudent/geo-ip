<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\GeoIPRequest;
use App\Models\GeoIP;
use Illuminate\Support\Facades\Http;
use Cache;

class GeoIPController extends Controller
{
    public function index(Request $request)
    {
        $ipAddress = $request->ip();
        $geoip = session('geoip');
        $ipFetched = session('ipFetched', false);
    
        return view('welcome', [
            'userIp' => $ipAddress,
            'geoip' => $geoip,
            'oldInput' => session('oldInput'),
            'ipFetched' => $ipFetched,
        ]);
    }
    

    public function query(GeoIPRequest $request)
    {
        $ip = $request->input('ip');

        Cache::put('userIp', $ip, 60); 

        $response = Http::get("http://ip2c.org/{$ip}");
    
        if ($response->successful()) {
            $data = explode(";", $response->body());
    
            $geoip = [
                'ip' => $ip,
                'country_code' => $data[1],
                'country_name' => $data[2],
                'ip_fetched' => true,
            ];

            // Store GeoIP information in the cache
            Cache::put('geoip', $geoip, 60); 
            Cache::put('ipFetched', true, 60);
    
            return redirect()->route('geoip.index')->with('geoip', $geoip)->with('userIp', $ip);
        } else {
            return redirect()->route('geoip.index')
                ->with('message', 'Failed to fetch GeoIP information.')
                ->with('oldInput', $request->input('ip'));
        }
    }
}
