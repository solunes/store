<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class ProductGroup extends Model {
	
	protected $table = 'product_groups';
	public $timestamps = false;

	/* Creating rules */
	public static $rules_create = array(
		'parent_id'=>'required',
		'name'=>'required',
		'image'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'parent_id'=>'required',
		'name'=>'required',
		'image'=>'required',
	);
	
}