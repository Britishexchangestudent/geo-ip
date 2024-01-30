<?php

// app/Http/Requests/GeoIPRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeoIPRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ip' => 'required|ip',
        ];
    }

    public function messages()
    {
        return [
            'ip.ip' => 'Invalid IP address format.',
        ];
    }
}

