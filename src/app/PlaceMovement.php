<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class PlaceMovement extends Model {
	
	protected $table = 'place_movements';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'place_from_id'=>'required',
		'place_to_id'=>'required',
		'account_from_id'=>'required',
		'account_to_id'=>'required',
		'name'=>'required',
		'currency_id'=>'required',
		'amount'=>'required|numeric|min:0',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'place_from_id'=>'required',
		'place_to_id'=>'required',
		'account_from_id'=>'required',
		'account_to_id'=>'required',
		'name'=>'required',
		'currency_id'=>'required',
		'amount'=>'required|numeric|min:0',
	);
    
    public function place_to() {
        return $this->belongsTo('Solunes\Store\App\Place', 'place_to_id');
    }
            
    public function place_from() {
        return $this->belongsTo('Solunes\Store\App\Place', 'place_from_id');
    }

    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }
     
    public function account_to() {
        return $this->belongsTo('Solunes\Store\App\Account', 'account_to_id');
    }
            
    public function account_from() {
        return $this->belongsTo('Solunes\Store\App\Account', 'account_from_id');
    }

}