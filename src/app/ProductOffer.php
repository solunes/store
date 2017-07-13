<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class ProductOffer extends Model {
	
	protected $table = 'product_offers';
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
	                        
    public function parent() {
        return $this->belongsTo('Solunes\Store\App\Product');
    }

    public function getSummaryLabelAttribute() {
    	$return = '<h4>Oferta</h4>';
        if($this->type=='discount_percentage'){
            $return .= '<h3>- '.$this->value.' %</h3>';
        } else if($this->type=='discount_value') {
            $return .= '<h3 class="small">- '.$this->value.' <span>Bs.</span></h3>';
        } else if($this->type=='discount_quantities') {
            $return .= '<h3 class="width">'.$this->value.'</h3>';
        }
        return $return;
    }

}