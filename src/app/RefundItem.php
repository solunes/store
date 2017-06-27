<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class RefundItem extends Model {
	
	protected $table = 'refund_items';
	public $timestamps = false;

	/* Creating rules */
	public static $rules_create = array(
		'parent_id'=>'required',
		'product_id'=>'required',
		'currency_id'=>'required',
		'initial_quantity'=>'required',
		'initial_amount'=>'required',
		'refund_quantity'=>'required',
		'refund_amount'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'parent_id'=>'required',
		'product_id'=>'required',
		'currency_id'=>'required',
		'initial_quantity'=>'required',
		'initial_amount'=>'required',
		'refund_quantity'=>'required',
		'refund_amount'=>'required',
	);

    public function parent() {
        return $this->belongsTo('Solunes\Store\App\Refund');
    }

    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }

    public function product() {
        return $this->belongsTo('Solunes\Store\App\Product');
    }

}