<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'rules';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name'
    ];

    public function shedules() {
		return $this->hasMany('App\Models\Schedule');
	}

    public function users() {
		return $this->belongsToMany('App\Models\User')->withPivot(['date','start_time','finish_time','observation']);
	}
}
