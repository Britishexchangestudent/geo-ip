<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoIP extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip',
        'country_code',
        'country_name',
        'ip_fetched',
    ];

    protected $table = 'geo_ips';

}
