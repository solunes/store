<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class ShippingCity extends Model {
	
	protected $table = 'shipping_cities';
	public $timestamps = false;

	/* Creating rules */
	public static $rules_create = array(
		'name'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'name'=>'required',
	);

    public function parent() {
        return $this->belongsTo('Solunes\Store\App\Shipping', 'parent_id');
    }

    public function city() {
        return $this->belongsTo('Solunes\Store\App\City');
    }

}