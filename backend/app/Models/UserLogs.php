<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLogs extends Model
{
    use  SoftDeletes;

    protected $table = 'user_logs';

    protected $fillable = [
        'user_id',
        'email',
        'last_login_at',
        'last_login_ip',
        'error_message',
        'browser_info'
    ];
}
