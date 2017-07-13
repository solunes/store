<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class SaleDelivery extends Model {
	
	protected $table = 'sale_deliveries';
	public $timestamps = false;

	/* Creating rules */
	public static $rules_create = array(
		'product_id'=>'required',
		'currency_id'=>'required',
		'quantity'=>'required',
		'price'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'product_id'=>'required',
		'currency_id'=>'required',
		'quantity'=>'required',
		'price'=>'required',
	);

    public function parent() {
        return $this->belongsTo('Solunes\Store\App\Sale');
    }

    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }

    public function product() {
        return $this->belongsTo('Solunes\Store\App\Product');
    }

    public function shipping() {
        return $this->belongsTo('Solunes\Store\App\Shipping');
    }

    public function getTotalPriceAttribute() {
        return round($this->price*$this->quantity);
    }

}