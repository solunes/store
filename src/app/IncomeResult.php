<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class IncomeResult extends Model {
	
	protected $table = 'income_results';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'name'=>'required',
		'type'=>'required',
		'currency_id'=>'required',
		'amount'=>'required|numeric|min:0',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'name'=>'required',
		'type'=>'required',
		'currency_id'=>'required',
		'amount'=>'required|numeric|min:0',
	);
    
    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }

    public function concept() {
        return $this->belongsTo('Solunes\Store\App\Concept');
    }

}