<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model {
	
	protected $table = 'taxes';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'name'=>'required',
		'type'=>'required',
		'percentage'=>'required|integer',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'name'=>'required',
		'type'=>'required',
		'percentage'=>'required|integer',
	);

}