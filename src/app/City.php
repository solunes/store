<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class City extends Model {
	
	protected $table = 'cities';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'region_id'=>'required',
		'name'=>'required',
		'active'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'region_id'=>'required',
		'name'=>'required',
		'active'=>'required',
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

    public function region() {
        return $this->belongsTo('Solunes\Store\App\Region');
    }

}