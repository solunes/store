<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;
use Solunes\Master\App\Traits\Section;

class Account extends Model {
	
	protected $table = 'accounts';
	public $timestamps = true;

    use Section;

	/* Creating rules */
	public static $rules_create = array(
		'concept_id'=>'required',
		'name'=>'required',
		'code'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'concept_id'=>'required',
		'name'=>'required',
		'code'=>'required',
	);
    
    public function concept() {
        return $this->belongsTo('Solunes\Store\App\Concept');
    }
         
    public function place_accountability() {
        return $this->hasMany('Solunes\Store\App\PlaceAccountability');
    }

}