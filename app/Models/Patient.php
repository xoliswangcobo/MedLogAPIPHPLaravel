<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends User
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bedNumber', 'ward'
    ];
}
