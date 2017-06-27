<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;
use Solunes\Master\App\Traits\Section;

class Concept extends Model {
	
	protected $table = 'concepts';
	public $timestamps = true;

	use Section;

	/* Creating rules */
	public static $rules_create = array(
		'code'=>'required',
		'type'=>'required',
		'name'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'code'=>'required',
		'type'=>'required',
		'name'=>'required',
	);

    public function accounts() {
        return $this->hasMany('Solunes\Store\App\Account');
    }
    
    public function account() {
        return $this->hasOne('Solunes\Store\App\Account');
    }

}