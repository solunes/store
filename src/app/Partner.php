<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model {
	
	protected $table = 'partners';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'name'=>'required',
		'return_percentage'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'name'=>'required',
		'return_percentage'=>'required',
	);

    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }

    public function account() {
        return $this->belongsTo('Solunes\Store\App\Account');
    }

    public function partner_details() {
        return $this->hasMany('Solunes\Store\App\PartnerDetail', 'parent_id');
    }

    public function partner_movements() {
        return $this->hasMany('Solunes\Store\App\PartnerMovement', 'parent_id');
    }

}