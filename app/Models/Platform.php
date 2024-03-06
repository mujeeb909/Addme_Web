<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    protected $table = 'platforms';

    protected $fillable = ['name','icon','title','description','status',
    'tenant_id','client_id', 'client_secret', 'redirect_uri'];
}
