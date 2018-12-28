<?php
/**
 * Model genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
	protected $table = 'histories';
	
	protected $hidden = [
        
    ];

	protected $guarded = [];

	protected $dates = ['deleted_at'];

	public function currency()
	{
		return $this->belongsTo('App\Models\Currency', 'Currency');
	}
}
