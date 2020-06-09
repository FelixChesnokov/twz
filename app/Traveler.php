<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Traveler extends Model
{
    protected $table = 'travelers';

    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'traveler_name',
        'traveler_email',
        'departure_date',
        'return_date',
        'destination_country',
        'destination_city'
    ];
}
