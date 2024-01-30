<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeoIP;
use Illuminate\Support\Facades\Http;

class GeoIPController extends Controller
{
    public function showForm()
    {
        $userIp = request()->ip();
        $geoip = session('geoip');
    
        return view('welcome', [
            'userIp' => $userIp,
            'geoip' => $geoip,
            'oldInput' => session('oldInput'),
        ]);
    }
    
    public function query(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
        ]);
    
        $ip = $request->input('ip');
    
        // Additional validation for the IP format
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return redirect()->route('geoip.show')
                ->with('message', 'Invalid IP address format.')
                ->with('oldInput', $ip);
        }
    
        try {
            $response = Http::get("http://ip2c.org/{ip}", ['ip' => $ip]);
    
            if ($response->successful()) {
                $data = explode(";", $response->body());
    
                // Check if the response has the expected data
                if (count($data) >= 4) {
                    $geoip = [
                        'ip' => $ip,
                        'country_code' => $data[1],
                        'country_name' => $data[3],
                        'ip_fetched' => true,
                    ];
    
                    GeoIP::create($geoip);
    
                    session(['geoip' => $geoip, 'userIp' => $ip, 'ipFetched' => true]);
    
                    return redirect()->route('geoip.show')->with('geoip', $geoip)->with('userIp', $ip);
                } else {
                    return redirect()->route('geoip.show')
                        ->with('message', 'Unexpected GeoIP API response format.')
                        ->with('oldInput', $request->input('ip'));
                }
            } else {
                return redirect()->route('geoip.show')
                    ->with('message', 'Failed to fetch GeoIP information. API response status: ' . $response->status())
                    ->with('oldInput', $request->input('ip'));
            }
        } catch (\Exception $e) {
            // Handle network errors or other exceptions
            return redirect()->route('geoip.show')
                ->with('message', 'Error: ' . $e->getMessage())
                ->with('oldInput', $request->input('ip'));
        }
    }
    
}
