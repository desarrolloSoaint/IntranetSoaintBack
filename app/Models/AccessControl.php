<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessControl extends Model
{
    use HasFactory;
    
    protected $table = 'rule_user';

    protected $fillable = [
        'date',
        'start_time',
        'finish_time',
        'observation',
        'rule_id',
        'user_id'
    ];
}
