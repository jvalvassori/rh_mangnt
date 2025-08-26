<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{

    protected $fillable = [
        'address',
        'zip_code',
        'city',
        'phone',
        'salary',
        'admission_date'
    ];
    public function user()
    {
        // each user has one details / each user_details belongs o a single user
        return $this->belongsTo(User::class);
    }
}
