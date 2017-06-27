<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class TransactionCode extends Model {
	
	protected $table = 'transaction_codes';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'type'=>'required',
		'code'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'type'=>'required',
		'code'=>'required',
	);

}