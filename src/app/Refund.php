<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model {
	
	protected $table = 'refunds';
	public $timestamps = true;

    /* Creating rules */
    public static $rules_create_refund = array(
        'reference'=>'required',
    );

	/* Creating rules */
	public static $rules_create = array(
        'user_id'=>'required',
        'sale_id'=>'required',
        'currency_id'=>'required',
        'place_id'=>'required',
        'amount'=>'required',
        'reference'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
        'user_id'=>'required',
        'sale_id'=>'required',
        'currency_id'=>'required',
        'place_id'=>'required',
        'amount'=>'required',
        'reference'=>'required',
	);
		
    public function user() {
        return $this->belongsTo('App\User');
    }	

    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }   
        
    public function refund_items() {
        return $this->hasMany('Solunes\Store\App\RefundItem', 'parent_id');
    }
        
    public function sale() {
        return $this->belongsTo('Solunes\Store\App\Sale');
    }

}