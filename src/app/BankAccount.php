<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model {
	
	protected $table = 'bank_accounts';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'currency_id'=>'required',
		'bank'=>'required',
		'account_number'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'currency_id'=>'required',
		'bank'=>'required',
		'account_number'=>'required',
	);

    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }
    
    public function account() {
        return $this->belongsTo('Solunes\Store\App\Account');
    }
    
    public function getNameAttribute() {
        return $this->bank.' - '.$this->account_number;
    }

}