<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model {
	
	protected $table = 'places';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'name'=>'required',
		'type'=>'required',
		'address'=>'required',
		'has_accountability'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'name'=>'required',
		'type'=>'required',
		'address'=>'required',
		'has_accountability'=>'required',
	);

	public function place_accountability() {
        return $this->hasMany('Solunes\Store\App\PlaceAccountability', 'parent_id');
    }

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