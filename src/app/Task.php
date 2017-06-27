<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model {
	
	protected $table = 'tasks';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'user_id'=>'required',
		'task'=>'required',
		'date'=>'required',
		'time'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'user_id'=>'required',
		'task'=>'required',
		'date'=>'required',
		'time'=>'required',
	);

    public function user() {
        return $this->belongsTo('App\User');
    }

}