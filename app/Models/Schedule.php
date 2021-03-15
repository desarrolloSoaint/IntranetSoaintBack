<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Schedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'schedule';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'start_time',
        'finish_time',
        'rule_id'
    ];

    public function rules() {
		return $this->belongsTo('App\Models\Rule');
	}
}
