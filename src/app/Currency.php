<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model {
	
	protected $table = 'currencies';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'name'=>'required',
		'type'=>'required',
		'in_accounts'=>'required',
		'main_exchange'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'name'=>'required',
		'type'=>'required',
		'in_accounts'=>'required',
		'main_exchange'=>'required',
	);

    public function getNameAttribute() {
        $return = $this->real_name;
    	/*if($this->type!='main'){
    		$return .= ' ('.$this->main_exchange.' Bs.)';
    	}*/
    	return $return;
    }

    public function getMainExchangeAttribute($value) {
    	if($value>0.1){
    		return round($value, 2);
    	}
    	return $value;
    }

}