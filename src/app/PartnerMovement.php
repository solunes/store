<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class PartnerMovement extends Model {
	
	protected $table = 'partner_movements';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'parent_id'=>'required',
		'place_id'=>'required',
		'currency_id'=>'required',
		'type'=>'required',
		'amount'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'parent_id'=>'required',
		'place_id'=>'required',
		'currency_id'=>'required',
		'type'=>'required',
		'amount'=>'required',
	);
    
    public function parent() {
        return $this->belongsTo('Solunes\Store\App\Partner');
    }
        
    public function place() {
        return $this->belongsTo('Solunes\Store\App\Place');
    }
    
    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }
    
}