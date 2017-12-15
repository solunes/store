<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model {
	
	protected $table = 'shippings';
	public $timestamps = true;

    /* Creating rules */
    public static $rules_create = array(
        'name'=>'required',
    );

    /* Updating rules */
    public static $rules_edit = array(
        'id'=>'required',
        'name'=>'required',
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

    public function city() {
        return $this->belongsTo('Solunes\Store\App\City');
    }

    public function shipping_cities() {
        return $this->hasMany('Solunes\Store\App\ShippingCity', 'parent_id');
    }

}