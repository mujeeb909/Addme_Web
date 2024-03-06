<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformIntegration extends Model
{
    use HasFactory;

    protected $table = 'platforms_integrations';

    protected $fillable = ['user_id','platform_id','code','refresh_token', 'access_token', 'expires_in'];
}


