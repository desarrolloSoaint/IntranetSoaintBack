<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
	use SoftDeletes;
	
    protected $table = 'roles';

	protected $dates = ['deleted_at'];

	protected $fillable = ['type'];

	public function user() {
		return $this->hasMany('App\Models\User');
	}
}
