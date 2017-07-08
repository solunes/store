<?php

namespace Solunes\Store\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Asset;

class CustomAdminController extends Controller {

	protected $request;
	protected $url;

	public function __construct(UrlGenerator $url) {
	  $this->middleware('auth');
	  $this->middleware('permission:dashboard');
	  $this->prev = $url->previous();
	  $this->module = 'admin';
	}

	public function checkFields() {
		$fields = \Solunes\Master\App\Field::where('display_list', 'none')->get();
		foreach($fields as $field){
			$field->display_list = 'excel';
			$field->save();
		}
	}

	public function getIndex() {
		$items['sales'] = ['url'=>url('admin/create-sale'), 'title'=>'VENTAS TIENDA'];
		$items['web_sales'] = ['url'=>url('admin/sales-detail-report?initial_date=&initial_date_submit=&end_date=&end_date_submit=&currency_id=1&place_id=web'), 'title'=>'VENTAS WEB'];
		$items['online_purchases'] = ['url'=>url('admin/model-list/online-sale'), 'title'=>'PEDIDOS ONLINE'];
		$items['create_purchase'] = ['url'=>url('admin/model/product/create'), 'title'=>'COMPRA DE PRODUCTOS'];
		$items['messages'] = ['url'=>url('admin/my-inbox'), 'title'=>'MENSAJERIA'];
		$items['international_sales'] = ['url'=>url('admin/model-list/international-sale'), 'title'=>'ENVIOS DEL EXTERIOR'];
		$items['expenses'] = ['url'=>url('admin/model/expense/create'), 'title'=>'EGRESOS - PAGOS'];
		$items['reports'] = ['url'=>url('admin/sales-report'), 'title'=>'INFORMES - REPORTES'];
		$items['search_product'] = ['url'=>url('admin/search-product'), 'title'=>'BUSCAR PRODUCTO'];
		$array['items'] = $items;
      	return view('store::list.dashboard-mesh', $array);
	}

	/* Módulo de Contabilidad */

	public function checkCurrencies($value) {
		$currencies = \Solunes\Store\App\Currency::get();
		foreach($currencies as $currency){
			foreach($currencies as $currency_2){
				print_r('Main: '.$currency->name.' - Sub: '.$currency_2->name.': '.\Func::calculate_currency($value, $currency, $currency_2).'<br>');
			}
		}
	}

	public function pendingPaymentRegister($type, $id) {
		$array['currencies'] = \Solunes\Store\App\Currency::where('in_accounts', 1)->get()->lists('real_name', 'id');
		$array['type'] = $type;
		if($type=='accounts-payable'){
			$array['pending_payment'] = \Solunes\Store\App\AccountsPayable::find($id);
		} else if($type=='accounts-receivable'){
			$array['pending_payment'] = \Solunes\Store\App\AccountsReceivable::find($id);
		}
		$array['paid_amount'] = $array['pending_payment']->paid_amount;
		$array['pending_amount'] = $array['pending_payment']->amount - $array['paid_amount'];
      	return view('store::modal.pending-payment-register', $array);
	}

    public function postPendingPaymentRegister(Request $request) {
    	$type = $request->input('type');
		if($type=='accounts-payable'){
	    	$pending_payment = \Solunes\Store\App\AccountsPayable::find($request->input('pending_payment_id'));
		} else if($type=='accounts-receivable'){
	    	$pending_payment = \Solunes\Store\App\AccountsReceivable::find($request->input('pending_payment_id'));
		}
        $validator = \Validator::make($request->all(), \Solunes\Store\App\AccountsPayable::$rules_register_payment);
	    if($validator->passes()&&$pending_payment) {

	        $amount = $request->input('amount');
	        $account_cash = \Solunes\Store\App\Account::getCode('asset_cash_small')->id;
	        if($type=='accounts-receivable'){
	        	$account_pending = \Solunes\Store\App\Account::getCode('asset_ctc')->id;
	        	$cash_type = 'debit';
	        	$pending_type = 'credit';
	            $name = 'Cuenta pendiente cobrada: '.$pending_payment->name;
	            $pending_payment->amount_paid = $pending_payment->amount_paid + $amount;
		        if($sale = $pending_payment->sale){
		        	$pending_difference = $sale->amount / $amount;
		        	foreach($sale->sale_items as $subitem){
		        		$pending = $subitem->total * $pending_difference;
		        		$new_pending = $subitem->pending - $pending;
		        		if($pending_payment->amount_paid==$pending_payment->amount){
		        			$subitem->pending = 0;
		        		} else {
		        			$subitem->pending = $pending;
		        		}
		        	}
		        }
	        } else if($type=='accounts-payable') {
	        	$account_pending = \Solunes\Store\App\Account::getCode('liability_ctp')->id;
	        	$cash_type = 'credit';
	        	$pending_type = 'debit';
	            $name = 'Cuenta pendiente pagada: '.$pending_payment->name;
	            $pending_payment->amount_paid = $pending_payment->amount_paid + $amount;
	        }
	        if($pending_payment->amount_paid==$pending_payment->amount){
	        	$pending_payment->status = 'paid';
	        }
	        $pending_payment->save();

	        $arr[] = \Store::register_account($pending_payment->place_id, $cash_type, $account_cash, $pending_payment->currency_id, $amount, $name) + ['pending_payment_id'=>$pending_payment->id];
	        $arr[] = \Store::register_account($pending_payment->place_id, $pending_type, $account_pending, $pending_payment->currency_id, $amount, $name);
	        \Store::register_account_array($arr);

			return ['redireccionar'=>true];
	    } else {
			return redirect('admin/pending-payment-register/'.$pending_payment->id)->with('message_error', 'Debe llenar todos los campos y al menos un producto para enviarlo.')->withErrors($validator)->withInput();
	    }
    }

	public function pendingPaymentUnpaid($type, $id) {
		$currencies = \Solunes\Store\App\Currency::where('in_accounts', 1)->get()->lists('real_name', 'id');
		if($type=='accounts-payable'){
			$pending_payment = \Solunes\Store\App\AccountsPayable::find($id);
		} else if($type=='accounts-receivable'){
			$pending_payment = \Solunes\Store\App\AccountsReceivable::find($id);
		}
		$paid_amount = $pending_payment->paid_amount;
		$pending_amount = $pending_payment->amount - $paid_amount;
		if($pending_payment->sale_id){
            $asset_ctc = \Solunes\Store\App\Account::getCode('asset_ctc')->id;
            $expense_unpaid_credit = \Solunes\Store\App\Account::getCode('expense_unpaid_credit')->id;
            $name = 'Cuenta por cobrar NO pagada por venta.';
            $arr[] = \Store::register_account($pending_payment->place_id, 'debit', $expense_unpaid_credit, $pending_payment->currency_id, $pending_amount, $name);
            $arr[] = \Store::register_account($pending_payment->place_id, 'credit', $asset_ctc, $pending_payment->currency_id, $pending_amount, $name);
            \Store::register_account_array($arr);
		}
		$pending_payment->status = 'unpaid';
		$pending_payment->save();
		return redirect($this->prev)->with('message_success', 'La cuenta pendiente fue dada de baja correctamente.');
	}

	/* Módulo de Productos */
   
    public function searchProduct($id = NULL) {
    	$array = ['i'=>NULL, 'dt'=>'create'];
    	$array['node'] = \Solunes\Master\App\Node::where('name', 'product')->first();
        $categories = \Solunes\Store\App\Category::has('products')->with('products')->orderBy('name', 'ASC')->get();
        $product_options = [''=>'-'];
        foreach($categories as $category){
            foreach($category->products as $product){
                $product_options[$category->name][$product->id] = $product->name.' ('.$product->barcode.')';
            }
        }
		$array['products'] = $product_options;
    	if($id){
    		$array['product'] = \Solunes\Store\App\Product::find($id);
    	} else {
    		$array ['product'] = NULL;
    	}
      	return view('store::item.search-product', $array);
	}

    public function generateBarcodesPdf() {
    	$products = \Solunes\Store\App\Product::where('printed', 0)->get();
    	$array = [];
    	foreach($products as $product){
    		$code = \Asset::generate_barcode_image($product->barcode);
    		$array[] = ['image'=>'<img src="data:image/png;base64,'.$code.'" />', 'name' => $product->name];
    		$product->printed = 1;
    		$product->save();
    	}
        return \PDF::loadView('store::pdf.product-barcodes', ['products'=>$array])->setPaper('letter')->setOption('margin-top', 12)->setOption('margin-left', 3)->setOption('margin-right', 0)->setOption('margin-bottom', 0)->stream('bulk_barcode.pdf');
	}

	public function getCheckProduct($id) {
		$item = \Solunes\Store\App\Product::with('currency')->find($id);
      	return ['name'=>$item->name, 'price'=>$item->price, 'no_invoice_price'=>$item->no_invoice_price, 'currency'=>$item->currency->name, 'quantity'=>$item->total_stock];
	}
    
	public function getPurchaseAddProduct($purchase_id, $product_id) {
		$array['purchase'] = \Solunes\Store\App\Purchase::find($purchase_id);
		$array['parent_id'] = $purchase_id;
		$array['product'] = \Solunes\Store\App\Product::find($product_id);
		$array['currencies'] = \Solunes\Store\App\Currency::get()->lists('name','id');
		$array['currencies_array'] = json_encode(\App\Currency::get()->lists('main_exchange','id')->toArray());
      	return view('store::modal.purchase-add-product', $array);
	}

    public function postPurchaseAddProduct(Request $request) {
	  $purchase = \Solunes\Store\App\Purchase::find($request->input('parent_id'));
      $validator = \Validator::make($request->all(), \Solunes\Store\App\PurchaseProduct::$rules_create_order);
	  if($validator->passes()&&$purchase) {

	  	$product = \Solunes\Store\App\Product::find($request->input('product_id'));

		$item = new \Solunes\Store\App\PurchaseProduct;
		$item->parent_id = $purchase->id;
		$item->product_id = $request->input('product_id');
		$item->quantity = $request->input('quantity');
		$item->currency_id = $request->input('currency_id');
		$item->cost = $request->input('cost');
		$item->save();
		return ['redireccionar'=>true];
	  } else {
		return redirect('admin/purchase-add-product/'.$purchase->id.'/'.$request->input('product_id'))->with('message_error', 'Debe llenar todos los campos y al menos un producto para enviarlo.')->withErrors($validator)->withInput();
	  }
    }

	public function getChangePurchaseStatus($id, $status) {
		$item = \Solunes\Store\App\Purchase::find($id);
		$item->status = $status;
		$item->save();
      	return redirect($this->prev);
	}

	public function getTransferProductStock($product_stock_id) {
		$array['product_stock'] = \Solunes\Store\App\ProductStock::find($product_stock_id);
		$array['product'] = $array['product_stock']->parent;
		$array['places'] = \Solunes\Store\App\Place::where('id', '!=', $array['product_stock']->place_id)->lists('name', 'id');
      	return view('store::modal.transfer-product-stock', $array);
	}

    public function postTransferProductStock(Request $request) {
	  $product_stock = \Solunes\Store\App\ProductStock::find($request->input('product_stock_id'));
      $validator = \Validator::make($request->all(), \Solunes\Store\App\ProductStock::$rules_transfer);
	  if($validator->passes()&&$product_stock) {

	  	$place = \Solunes\Store\App\Place::find($request->input('place_id'));
		$name = 'Transferencia recibida de '.$product_stock->place->name;
        $response = \Store::inventory_movement($place, $product_stock->parent, 'move_in', $product_stock->quantity, $name, 'register_product_movement', $product_stock);
		$name = 'Transferencia realizada a '.$place->name;
        $response = \Store::inventory_movement($product_stock->place, $product_stock->parent, 'move_out', $product_stock->quantity, $name, 'register_product_movement', $product_stock);

		return ['redireccionar'=>true];
	  } else {
		return redirect('admin/transfer-product-stock/'.$product_stock->id)->with('message_error', 'Debe llenar todos los campos.')->withErrors($validator)->withInput();
	  }
    }

	public function getRemoveProductStock($product_stock_id) {
		$array['product_stock'] = \Solunes\Store\App\ProductStock::find($product_stock_id);
		$array['product'] = $array['product_stock']->parent;
      	return view('store::modal.remove-product-stock', $array);
	}

    public function postRemoveProductStock(Request $request) {
	  $product_stock = \Solunes\Store\App\ProductStock::find($request->input('product_stock_id'));
      $validator = \Validator::make($request->all(), \Solunes\Store\App\ProductStock::$rules_remove);
	  if($validator->passes()&&$product_stock) {

		$name = 'Stock removido: '.$request->input('name');
        $response = \Store::inventory_movement($product_stock->place, $product_stock->parent, 'move_out', $product_stock->quantity, $name, 'register_product_drop', $product_stock);

		return ['redireccionar'=>true];
	  } else {
		return redirect('admin/remove-product-stock/'.$product_stock->id)->with('message_error', 'Debe llenar todos los campos.')->withErrors($validator)->withInput();
	  }
    }

	/* Módulo de Ventas */

	public function getCalculateTotal($amount, $currency_id) {
		$main_currency = \Solunes\Store\App\Currency::find($currency_id);
		$item_currency = \Solunes\Store\App\Currency::find(1);
		return \Store::calculate_currency($amount, $main_currency, $item_currency);
	}

	public function getCreateSale() {
		$array['places'] = \Solunes\Store\App\Place::where('type', 'store')->lists('name', 'id');
		$array['currencies'] = \Solunes\Store\App\Currency::where('in_accounts',1)->get()->lists('name', 'id');
		$array['invoices'] = [0=>'Sin Factura', 1=>'Con Factura'];
		$array['types'] = ['normal'=>'En Tienda', 'web'=>'Web', 'online'=>'Online'];
		$array['i'] = NULL;
		$array['dt'] = 'create';
		$array['action'] = 'create';
		$array['model'] = 'sale';
		$array['currency'] = \Solunes\Store\App\Currency::where('type', 'main')->first();
		$array['node'] = \Solunes\Master\App\Node::where('name', 'product')->first();
        $categories = \Solunes\Store\App\Category::has('products')->with('products')->orderBy('name', 'ASC')->get();
        $product_options = [''=>'-'];
        foreach($categories as $category){
            foreach($category->products as $product){
            	if($product->total_stock>0){
                	$product_options[$category->name][$product->id] = $product->name.' ('.$product->barcode.')';
            	}
            }
        }
		$array['products'] = $product_options;
		$array['currency_dollar'] = \Solunes\Store\App\Currency::find(2);
      	return view('store::item.create-sale', $array);
	}

    public function postCreateSale(Request $request) {
      $validator = \Validator::make($request->all(), \Solunes\Store\App\Sale::$rules_create_sale);
      if($request->input('paid_amount')<$request->input('amount')&&!$request->input('credit')){
		return redirect($this->prev)->with('message_error', 'Debe introducir un monto pagado mayor al total, o incluir la opción de crédito.')->withErrors($validator);
      }
	  if($validator->passes()&&$request->input('product_id')[0]) {

		$item = new \Solunes\Store\App\Sale;
		$item->user_id = auth()->user()->id;
		$item->place_id = $request->input('place_id');
		$item->currency_id = 1;
		$item->amount = $request->input('amount');
		$item->change = $request->input('change');
		$item->cash_bob = $request->input('cash_bob');
		$item->cash_usd = $request->input('cash_usd');
		$item->pos_bob = $request->input('pos_bob');
		$item->paid_amount = $request->input('paid_amount');
		$item->invoice = $request->input('invoice');
		$item->invoice_name = $request->input('invoice_name');
		$item->invoice_nit = $request->input('invoice_nit');
		$item->type = $request->input('type');
		$item->exchange = $request->input('exchange');
		$item->shipping_cost = $request->input('shipping_cost');
		$item->credit = $request->input('credit');
		if($item->credit){
			$item->credit_amount = $request->input('credit_amount');
			$item->credit_due = $request->input('credit_due');
			$item->credit_details = $request->input('credit_details');
			$credit_percentage = $request->input('amount') / $request->input('credit_amount');
		}
		$item->save();

		$total_count = count($request->input('product_id'));
		$count = 0;
		$pending_sum = 0;
		foreach($request->input('product_id') as $product_key => $product_id){
			if($product = \Solunes\Store\App\Product::find($product_id)){
				$subitem = new \Solunes\Store\App\SaleItem;
				$subitem->parent_id = $item->id;
				$subitem->product_id = $product->id;
				$subitem->currency_id = $product->currency_id;
				$subitem->price = $request->input('price')[$product_key];
				$subitem->quantity = $request->input('quantity')[$product_key];
				$subitem->total = $subitem->price * $subitem->quantity;
				if($item->credit&&$item->credit_amount>0){
					$pending = round($credit_percentage * $total, 2);
					$pending_sum += $pending;
					$subitem->pending = $pending;
					$count ++;
					if($total_count==$count){
						$diff = $item->credit_amount - $pending_sum;
						if($diff!=0){
							$subitem->pending = $subitem->pending + $diff;
						}
					}
				}
				$subitem->save();
			}
		}
		return redirect('admin/model/sale/view/'.$item->id)->with('message_success', 'La venta se realizó correctamente');
	  } else {
		return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos y al menos un producto para enviarlo.')->withErrors($validator);
	  }
    }

	public function getCreateRefund($sale_id = NULL) {
		$array['i'] = NULL;
		$array['dt'] = 'create';
		$array['action'] = 'create';
		$array['model'] = 'refund';
		$array['node'] = \Solunes\Master\App\Node::where('name', 'refund')->first();
		$array['sale_id'] = $sale_id;
		$array['places'] = \Solunes\Store\App\Place::lists('name','id');
		$array['products'] = \Solunes\Store\App\Product::lists('name','id');
		$sales = \Solunes\Store\App\Sale::orderBy('created_at', 'DESC');
        if(request()->has('initial_date')){
            $initial_date = request()->input('initial_date').' 00:00:00';
            $sales = $sales->where('created_at', '>', $initial_date);
        }
        if(request()->has('end_date')){
            $end_date = request()->input('end_date').' 23:59:59';
            $sales = $sales->where('created_at', '<', $end_date);
        }
        if(request()->has('product_id')){
            $product_id = request()->input('product_id');
        	$sales = $sales->whereHas('sale_items', function ($query) use($product_id) {
			    $query->where('product_id', $product_id);
			});
        }
        if(request()->has('place_id')){
            $place_id = request()->input('place_id');
            $sales = $sales->where('place_id', $place_id);
        }
		$array['sales'] = $sales->get();
		if(!$sale_id||count($array['sales'])==1){
		} else {
			$array['sale'] = \Solunes\Store\App\Sale::find($sale_id);
		}
      	return view('store::item.create-refund', $array);
	}

    public function postCreateRefund(Request $request) {
      $validator = \Validator::make($request->all(), \Solunes\Store\App\Refund::$rules_create_refund);
	  if($validator->passes()) {

	  	$sale = \Solunes\Store\App\Sale::find($request->input('sale_id'));

		$item = new \Solunes\Store\App\Refund;
		$item->user_id = auth()->user()->id;
		$item->place_id = $sale->place_id;
		$item->currency_id = $sale->currency_id;
		$item->sale_id = $request->input('sale_id');
		$item->reference = $request->input('reference');
		$item->amount = $request->input('amount');
		$item->save();

		foreach($request->input('product_id') as $product_key => $product_id){
			if($request->input('refund_quantity')[$product_key]>0){
				$subitem = new \Solunes\Store\App\RefundItem;
				$subitem->parent_id = $item->id;
				$subitem->product_id = $request->input('product_id')[$product_key];
				$subitem->currency_id = $item->currency_id;
				$subitem->initial_quantity = $request->input('initial_quantity')[$product_key];
				$subitem->initial_amount = $request->input('initial_amount')[$product_key];
				$subitem->refund_quantity = $request->input('refund_quantity')[$product_key];
				$subitem->refund_amount = $request->input('refund_amount')[$product_key];
				$subitem->sale_item_id = $request->input('sale_item_id')[$product_key];
				$subitem->save();
			}
		}
		return redirect('admin/model/refund/view/'.$item->id)->with('message_success', 'La devolución se realizó correctamente');
	  } else {
		return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos y al menos un producto para enviarlo.')->withErrors($validator);
	  }
    }

}