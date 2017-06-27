<?php

namespace Solunes\Store\App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Product extends Model {
	
	protected $table = 'products';
	public $timestamps = true;

    use Sluggable, SluggableScopeHelpers;
    public function sluggable(){
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
    
	/* Creating rules */
	public static $rules_create = array(
		'category_id'=>'required',
        'currency_id'=>'required',
        'external_currency_id'=>'required',
        'partner_id'=>'required',
        'partner_transport_id'=>'required',
        'barcode'=>'required',
        'name'=>'required',
        'cost'=>'required',
        'price'=>'required',
        'no_invoice_price'=>'required',
        'printed'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
        'category_id'=>'required',
        'currency_id'=>'required',
        'external_currency_id'=>'required',
        'partner_id'=>'required',
        'partner_transport_id'=>'required',
        'barcode'=>'required',
        'name'=>'required',
        'cost'=>'required',
        'price'=>'required',
        'no_invoice_price'=>'required',
        'printed'=>'required',
	);

    public function category() {
        return $this->belongsTo('Solunes\Store\App\Category');
    }

    public function currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }

    public function external_currency() {
        return $this->belongsTo('Solunes\Store\App\Currency');
    }

    public function partner() {
        return $this->belongsTo('Solunes\Store\App\Partner');
    }

    public function partner_transport() {
        return $this->belongsTo('Solunes\Store\App\Partner');
    }

    public function product_group() {
        return $this->belongsTo('Solunes\Store\App\ProductGroup');
    }

    public function product_variations() {
        return $this->belongsToMany('Solunes\Store\App\Variation', 'product_variation', 'product_id', 'variation_id');
    }

    public function product_stocks() {
        return $this->hasMany('Solunes\Store\App\ProductStock', 'parent_id');
    }

    public function purchase_products() {
        return $this->hasMany('Solunes\Store\App\PurchaseProduct');
    }

    public function getTotalStockAttribute() {
        if(count($this->product_stocks)>0){
            return $this->product_stocks->sum('quantity');
        } else {
            return 0;
        }
    }

    public function item_get_after_vars($module, $node, $single_model, $id, $variables){
        //$variables['no_invoice_reduction'] = \App\Variable::where('name', 'reduccion_sin_factura')->first()->value;
        $variables['no_invoice_reduction'] = 16;
        return $variables;
    }

    public function item_child_after_vars($module, $node, $single_model, $id, $variables){
        //$variables['no_invoice_reduction'] = \App\Variable::where('name', 'reduccion_sin_factura')->first()->value;
        $variables['no_invoice_reduction'] = 16;
        return $variables;
    }

}