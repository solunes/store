<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class PartnerDetail extends Model {
	
	protected $table = 'partner_details';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'parent_id'=>'required',
		'currency_id'=>'required',
		'product_id'=>'required',
		'status'=>'required',
		'initial_quantity'=>'required',
		'quantity'=>'required',
		'investment'=>'required',
		'paid'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'parent_id'=>'required',
		'currency_id'=>'required',
		'product_id'=>'required',
		'status'=>'required',
		'initial_quantity'=>'required',
		'quantity'=>'required',
		'investment'=>'required',
		'paid'=>'required',
	);
    
    public function parent() {
        return $this->belongsTo('Solunes\Store\App\Partner');
    }
       
    public function partner_transport() {
        return $this->belongsTo('Solunes\Store\App\Partner');
    }

    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }
        
    public function product() {
        return $this->belongsTo('Solunes\Store\App\Product');
    }
        
    public function sale_item() {
        return $this->belongsTo('Solunes\Store\App\SaleItem');
    }

    public function getNameAttribute() {
        return $this->investment.' - '.$this->return.' ('.$this->currency->name.')';
    }

}