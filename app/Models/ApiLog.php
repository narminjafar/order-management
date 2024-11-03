<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'method', 'url', 'request_payload', 'response_payload', 'status_code', 'request_time'
    ];
}
