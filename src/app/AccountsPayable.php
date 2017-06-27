<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class AccountsPayable extends Model {
	
	protected $table = 'accounts_payable';
	public $timestamps = true;


	/* Register Payment rules */
	public static $rules_register_payment = array(
		'amount'=>'required|numeric|min:1',
	);

	/* Creating rules */
	public static $rules_create = array(
		'name'=>'required',
		'due_date'=>'required',
        'place_id'=>'required',
        'account_id'=>'required',
		'currency_id'=>'required',
		'amount'=>'required|numeric|min:1',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'name'=>'required',
		'due_date'=>'required',
		'place_id'=>'required',
        'account_id'=>'required',
		'currency_id'=>'required',
		'amount'=>'required|numeric|min:1',
	);
    
    public function place() {
        return $this->belongsTo('Solunes\Store\App\Place');
    }
    
    public function account() {
        return $this->belongsTo('Solunes\Store\App\Account');
    }

    public function account_details() {
        return $this->hasMany('Solunes\Store\App\PlaceAccountability', 'pending_payment_id', 'id')->where('type','credit');
    }

    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }
        
    public function sale() {
        return $this->belongsTo('Solunes\Store\App\Sale');
    }

    public function getPaidAmountAttribute() {
    	if(count($this->account_details)>0){
        	return $this->account_details->sum('amount');
    	} else {
    		return 0;
    	}
    }

    public function getPendingAmountAttribute() {
    	if($this->paid_amount>0){
        	return $this->amount - $this->paid_amount;
    	} else {
    		return $this->amount;
    	}
    }

}