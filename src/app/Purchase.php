<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model {
	
	protected $table = 'purchases';
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
                        
    public function place() {
        return $this->belongsTo('Solunes\Store\App\Place');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }

    public function purchase_products() {
        return $this->hasMany('Solunes\Store\App\PurchaseProduct', 'parent_id');
    }

    public function getTotalAttribute(){
    	$total = 0;
    	$currency = \Solunes\Store\App\Currency::first();
    	if(count($this->purchase_products)>0){
	    	foreach($this->purchase_products as $batch){
	    		$total += \Store::calculate_currency($batch->total, $currency, $batch->currency);
	    	}
    	}
    	return $total.' '.$currency->name;
    }

    public function item_get_after_vars($module, $node, $single_model, $id, $variables){
    	$variables['product_node_id'] = \Solunes\Master\App\Node::where('name', 'product')->first()->id;
    	return $variables;
    }

}