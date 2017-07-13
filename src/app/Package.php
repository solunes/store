<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model {
	
	protected $table = 'packages';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'place_id'=>'required',
		'currency_id'=>'required',
        'name'=>'required',
        'type'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'place_id'=>'required',
        'currency_id'=>'required',
        'name'=>'required',
        'type'=>'required',
	);

    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }

}