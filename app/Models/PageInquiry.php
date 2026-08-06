<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageInquiry extends Model
{
    protected $fillable = [
        'page_slug',
        'full_name',
        'email',
        'phone',
        'company',
        'message',
        'ip_address',
        'user_agent',
    ];
}
