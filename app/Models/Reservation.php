<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'email',
        'phone',
        'reservation_date',
        'guest_count',
        'status',
        'note',
    ];
}
