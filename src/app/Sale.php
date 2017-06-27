<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model {
	
	protected $table = 'sales';
	public $timestamps = true;

    /* Creating rules */
    public static $rules_create_sale = array(
        //'currency_id'=>'required',
        //'product_id'=>'required',
    );

	/* Creating rules */
	public static $rules_create = array(
        'currency_id'=>'required',
        'place_id'=>'required',
        'type'=>'required',
        'invoice'=>'required',
        'credit'=>'required',
        'online_sale'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
        'currency_id'=>'required',
        'place_id'=>'required',
        'type'=>'required',
        'invoice'=>'required',
        'credit'=>'required',
        'online_sale'=>'required',
	);
       
    public function place() {
        return $this->belongsTo('Solunes\Store\App\Place');
    }

    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }
		
    public function user() {
        return $this->belongsTo('App\User');
    }	

    public function sale_items() {
        return $this->hasMany('Solunes\Store\App\SaleItem', 'parent_id');
    }
        
    public function pending_payment() {
        return $this->hasOne('Solunes\Store\App\AccountsReceivable', 'sale_id')->where('status', 'holding');
    }

}