<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class PlaceAccountability extends Model {
	
	protected $table = 'place_accountability';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'parent_id'=>'required',
		'account_id'=>'required',
		'currency_id'=>'required',
		'name'=>'required',
		'type'=>'required',
		'amount'=>'required|numeric|min:0',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'parent_id'=>'required',
		'account_id'=>'required',
		'currency_id'=>'required',
		'name'=>'required',
		'type'=>'required',
		'amount'=>'required|numeric|min:0',
	);
    
    public function parent() {
        return $this->belongsTo('Solunes\Store\App\Place');
    }
    
    public function account() {
        return $this->belongsTo('Solunes\Store\App\Account');
    }

    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }

    public function other_accounts() {
        return $this->hasMany('Solunes\Store\App\PlaceAccountability', 'transaction_code', 'transaction_code')->where('id','!=',$this->id);
    }
    
}