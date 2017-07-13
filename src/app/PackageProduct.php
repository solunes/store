<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;

class PackageProduct extends Model {
	
	protected $table = 'package_products';
	public $timestamps = true;
    
	/* Creating rules */
	public static $rules_create = array(
        'product_id'=>'required',
        'status'=>'required',
        'quantity'=>'required',
        'currency_id'=>'required',
        'cost'=>'required',
        'partner_id'=>'required',
        'partner_transport_id'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
        'product_id'=>'required',
        'status'=>'required',
        'quantity'=>'required',
        'currency_id'=>'required',
        'cost'=>'required',
        'partner_id'=>'required',
        'partner_transport_id'=>'required',
	);
                        
    public function parent() {
        return $this->belongsTo('Solunes\Store\App\Purchase', 'parent_id');
    }

    public function product() {
        return $this->belongsTo('Solunes\Store\App\Product');
    }

}