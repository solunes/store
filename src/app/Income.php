<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class Income extends Model {
	
	protected $table = 'incomes';
	public $timestamps = true;

	/* Register Payment rules */
	public static $rules_register_payment = array(
		'amount'=>'required|numeric|min:1',
	);

	/* Creating rules */
	public static $rules_create = array(
		'name'=>'required',
		'place_id'=>'required',
		'account_id'=>'required',
		'currency_id'=>'required',
		'amount'=>'required|numeric|min:1',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'name'=>'required',
		'place_id'=>'required',
		'account_id'=>'required',
		'currency_id'=>'required',
		'amount'=>'required|numeric|min:1',
	);
    
    public function account() {
        return $this->belongsTo('Solunes\Store\App\Account');
    }
                        
    public function place() {
        return $this->belongsTo('Solunes\Store\App\Place');
    }

    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }
        
    public function sale() {
        return $this->belongsTo('Solunes\Store\App\Sale');
    }

}