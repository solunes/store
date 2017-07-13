<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class City extends Model {
	
	protected $table = 'cities';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'name'=>'required',
		'type'=>'required',
		'in_accounts'=>'required',
		'main_exchange'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'name'=>'required',
		'type'=>'required',
		'in_accounts'=>'required',
		'main_exchange'=>'required',
	);

    public function scopeActive($query) {
        return $query->where('active', 1);
    }

    public function scopeInactive($query) {
        return $query->where('active', 0);
    }

    public function scopeOrder($query) {
        return $query->orderBy('order', 'ASC');
    }

}