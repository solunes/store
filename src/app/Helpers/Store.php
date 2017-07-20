<?php 

namespace Solunes\Store\App\Helpers;

use Validator;

class Store {

    public static function check_category_children($category, $category_array = []) {
        $category_array[] = $category->id;
        if(count($category->children)>0){
          foreach($category->children as $subcategory){
            $category_array = \Store::check_category_children($subcategory, $category_array);
          }
        }
        return $category_array;
    }

    public static function calculate_currency($item_amount, $main_currency, $item_currency, $exchange = NULL) {
        if($main_currency->id!=$item_currency->id){
            if(!$exchange){
                $exchange = $item_currency->main_exchange;
            }
            if($main_currency->type!='main'){
                $item_amount = $item_amount / $exchange;
            } else {
                $item_amount = $item_amount * $exchange;
            }
        }
        return round($item_amount, 2);
    }

    public static function generate_code($type) {
        if($last = \Solunes\Store\App\TransactionCode::orderBy('code', 'DESC')->first()){
            $last_code = $last->code;
        } else {
            $last_code = 0;
        }
        $new = $last_code + 1;
        $new_code = new \Solunes\Store\App\TransactionCode;
        $new_code->type = $type;
        $new_code->code = $new;
        $new_code->save();
        return $new;
    }

    public static function register_sale_payment($sale, $payment_id, $currency_id, $status, $amount, $detail, $exchange = 1) {
        $sale_payment = new \Solunes\Store\App\SalePayment;
        $sale_payment->parent_id = $sale->id;
        $sale_payment->payment_id = $payment_id;
        $sale_payment->currency_id = $currency_id;
        $sale_payment->status = $status;
        $sale_payment->amount = $amount;
        $sale_payment->detail = $detail;
        $sale_payment->exchange = $exchange;
        $sale_payment->save();
        return $sale_payment;
    }

    public static function register_account($place_id, $type, $account_id, $currency_id, $amount, $name, $exchange = NULL) {
        return ['place_id'=>$place_id, 'type'=>$type, 'account_id'=>$account_id, 'currency_id'=>$currency_id, 'amount'=>$amount, 'name'=>$name, 'exchange'=>$exchange];
    }
        
    public static function register_account_array($array, $created_at = NULL, $transaction_code = NULL) {
        if(!$transaction_code){
            $transaction_code = \Store::generate_code('auto');
        }
        if(!$created_at){
            $created_at = date('Y-m-d H:i:s');
        }
        foreach($array as $item){
            if($item['amount']>0){
                $account_detail = new \Solunes\Store\App\PlaceAccountability;
                $account_detail->parent_id = $item['place_id'];
                $account_detail->transaction_code = $transaction_code;
                $account_detail->type = $item['type'];
                $account_detail->name = $item['name'];
                $account_detail->account_id = $item['account_id'];
                $account_detail->currency_id = $item['currency_id'];
                $account_detail->exchange = $item['exchange'];
                $account_detail->amount = $item['amount'];
                if(isset($item['pending_payment_id'])){
                    $account_detail->pending_payment_id = $item['pending_payment_id'];
                }
                $account_detail->created_at = $created_at;
                $account_detail->save();
            }
        }
    }

    public static function inventory_movement($place, $product, $type, $quantity, $name, $transaction, $item, $transaction_code = NULL) {

        // Crear Movimiento de Inventario
        $product_movement = new \Solunes\Store\App\InventoryMovement;
        $product_movement->place_id = $place->id;
        $product_movement->product_id = $product->id;
        $product_movement->type = $type;
        $product_movement->quantity = $quantity;
        $product_movement->name = $name;
        $product_movement->save();

        // Stock Product
        $real_quantity = $quantity;
        if($type=='move_out'){
            $real_quantity = -$quantity;
        }
        if($product_stock = \Solunes\Store\App\ProductStock::where('parent_id', $product->id)->where('place_id', $place->id)->first()){
            $product_stock->quantity = $product_stock->quantity + $real_quantity;
        } else {
            $product_stock = new \Solunes\Store\App\ProductStock;
            $product_stock->parent_id = $product->id;
            $product_stock->place_id = $place->id;
            $product_stock->initial_quantity = $quantity;
            $product_stock->quantity = $quantity;
        }
        $product_stock->save();

        // Purchase Product para Capital de Socios
        if($transaction!='register_product_purchase'&&$transaction!='register_product_movement'){
            $amount = 0;
            $purchase_products = $product->purchase_products()->where('status', 'holding')->orderBy('created_at','DESC');
            $purchase_products = $purchase_products->get();
            $remaining_quantity = $quantity;
            foreach($purchase_products as $purchase_product){
                // Ajuste de Inventario de Socio y Ganancia
                if($remaining_quantity>0){
                    if($type=='move_out'){
                        $remaining_quantity = $purchase_product->quantity - $remaining_quantity;
                        if($remaining_quantity<0){
                            $remaining_quantity = 0;
                            $quantity_purchase = $purchase_product->quantity;
                        } else {
                            $quantity_purchase = $remaining_quantity;
                        }
                        $purchase_product->quantity = $remaining_quantity;
                        if($transaction=='register_sale_item'){
                            $paid_amount = $item->total;
                            $paid_amount -= $item->pending;
                            if($item->parent->invoice){
                                $taxes = $paid_amount * 0.16;
                                $paid_amount -= $taxes;
                            }
                            $difference = $purchase_product->transport_investment - $purchase_product->transport_return;
                            if($paid_amount>0&&$difference > 0){
                                $paid_amount -= $difference;
                                if($paid_amount<0){
                                    $difference += $paid_amount;
                                }
                                $purchase_product->transport_return = $purchase_product->transport_return + $difference; 
                            } 
                            $difference = $purchase_product->investment - $purchase_product->return;
                            if($paid_amount>0&&$difference > 0){
                                $paid_amount -= $difference;
                                if($paid_amount<0){
                                    $difference += $paid_amount;
                                }
                                $purchase_product->return = $purchase_product->return + $difference; 
                            } 
                            if($paid_amount>0){
                                $purchase_product->profit = $purchase_product->profit + $paid_amount; 
                            } 
                            $max_profit = $purchase_product->investment * ($purchase_product->partner->return_percentage/100);
                            if($purchase_product->profit>$max_profit){
                                $purchase_product->profit = $max_profit;
                            }
                        } 
                    } else if($type=='move_in'){
                        $real_quantity = $purchase_product->initial_quantity - $purchase_product->quantity;
                        $remaining_quantity = $real_quantity - $remaining_quantity;
                        if($remaining_quantity<0){
                            $remaining_quantity = 0;
                            $quantity_purchase = $real_quantity;
                        } else {
                            $quantity_purchase = $remaining_quantity;
                        }
                        $purchase_product->quantity = $purchase_product->quantity + $quantity_purchase;
                        if($transaction=='register_refund_item') {
                            $paid_amount = $item->refund_amount;
                            if($paid_amount>0&&$purchase_product->profit > 0){
                                $paid_amount -= $purchase_product->profit;
                                if($paid_amount<0){
                                    $difference += $paid_amount;
                                }
                                $purchase_product->profit = $purchase_product->profit - $difference; 
                            } 
                            $difference = $purchase_product->transport_return - $purchase_product->transport_investment;
                            if($paid_amount>0&&$difference > 0){
                                $paid_amount -= $difference;
                                if($paid_amount<0){
                                    $difference += $paid_amount;
                                }
                                $purchase_product->transport_return = $purchase_product->transport_return - $difference; 
                            } 
                            $difference = $purchase_product->transport_return - $purchase_product->transport_investment;
                            if($paid_amount>0&&$difference > 0){
                                $paid_amount -= $difference;
                                if($paid_amount<0){
                                    $difference += $paid_amount;
                                }
                                $purchase_product->transport_return = $purchase_product->transport_return - $difference; 
                            } 
                        }
                    }
                    $purchase_product->save();
                }
            }
        }

        /* Crear cuentas de inventario */
        if($transaction!='register_product_purchase'){
            $currency_id = $product->currency_id;
            $amount = $product->cost * $quantity;
            $asset_stock = \Solunes\Store\App\Account::getCode('asset_stock')->id;
            if($transaction=='register_product_drop'){
                $expense_sale = \Solunes\Store\App\Account::getCode('expense_inventory_loss')->id;
            } else {
                $expense_sale = \Solunes\Store\App\Account::getCode('expense_sale')->id;
            }
            if($type=='move_out'){
                $stock_type = 'credit';
                $expense_type = 'debit';
            } else {
                $stock_type = 'debit';
                $expense_type = 'credit';
            }
            $arr[] = \Store::register_account($place->id, $stock_type, $asset_stock, $currency_id, $amount, $name);
            $arr[] = \Store::register_account($place->id, $expense_type, $expense_sale, $currency_id, $amount, $name);
            \Store::register_account_array($arr, $item->created_at, $transaction_code);
        }
    }

    public static function check_report_header($model, $places = [], $extra = NULL) {
        $date = date('Y-m-d');
        if(request()->has('currency_id')){
          $currency_id = request()->input('currency_id');
        } else {
          $currency_id = 1;
        }
        $currency = \Solunes\Store\App\Currency::find($currency_id);
        $currencies = \Solunes\Store\App\Currency::where('in_accounts', 1)->get()->lists('real_name', 'id');
        $array = ['i'=>NULL, 'dt'=>'create', 'currencies'=>$currencies, 'currency'=>$currency, 'datepicker_initial'=> $date, 'datepicker_end'=>$date];
        $path = request()->segment(2);
        if(request()->segment(3)){
            $path .= '/'.request()->segment(3);
        }
        $array['path'] = $path;
        if($date_item_i = $model->orderBy('created_at', 'ASC')->first()){
          $array['datepicker_initial'] = $date_item_i->created_at;
        }
        if($date_item_e = $model->orderBy('created_at', 'DESC')->first()){
          $array['datepicker_end'] = $date_item_e->created_at;
        }
        if(request()->has('period')&&request()->input('period')=='custom'){
            if(request()->has('initial_date')){
              $initial_date = request()->input('initial_date');
            } else {
              $initial_date = $date_item_i->created_at->format('Y-m-d');
            }
            if(request()->has('end_date')){
              $end_date = request()->input('end_date');
            } else {
              $end_date = $date_item_e->created_at->format('Y-m-d');
            }
        } else {
            if(!request()->has('period')||request()->input('period')=='month'){
                $i_date = 'first day of this month';
                $e_date = 'last day of this month';
            } else if(request()->input('period')=='year') {
                $i_date = date('Y-01-01');
                $e_date = date('Y-12-31');
            } else if(request()->input('period')=='day') {
                $i_date = $date;
                $e_date = $date;
            } else if(request()->input('period')=='week') {
                $i_date = 'monday this week';
                $e_date = 'sunday this week';
            }
            $initial_date = date("Y-m-d", strtotime($i_date));
            $end_date = date("Y-m-d", strtotime($e_date));
        }
        $array['initial_date'] = $initial_date;
        $array['end_date'] = $end_date;
        $array['i_date'] = $initial_date.' 00:00:00';
        $array['e_date'] = $end_date.' 23:59:59';
        $array['show_place'] = false;
        $array['show_account_id'] = false;
        if($extra&&$extra=='account_id'){
            $array['show_account_id'] = true;
            $array['accounts'] = \Solunes\Store\App\Account::lists('name','id');
            $array['current_account_id'] = 1;
            if(request()->has('account_id')){
                $array['current_account_id'] = request()->input('account_id');
            }
        }
        $array['places'] = ['all'=>'Consolidado']  + \Solunes\Store\App\Place::lists('name', 'id')->toArray() + $places;
        if(request()->has('place_id')){
            $array['place'] = request()->input('place_id');
        } else {
            $array['place'] = 'all';
        }
        if(isset($array['places'][$array['place']])){
            $array['place_name'] = $array['places'][$array['place']];
        }
        $array['periods'] = ['day'=>'Hoy', 'week'=>'Esta Semana', 'month'=>'Este Mes', 'year'=>'Este Año', 'custom'=>'Personalizado'];
        // URL
        $url = request()->fullUrl();
        if(strpos($url, '?') !== false){
            $url .= '&download-pdf=true';
        } else {
            $url .= '?download-pdf=true';
        }
        $array['url'] = $url;
        return $array;
    }

    public static function calculate_shipping_cost($shipping_id, $city_id, $weight) {
        $shipping = \Solunes\Store\App\Shipping::find($shipping_id);
        $shipping_city = $shipping->shipping_cities()->where('city_id', $city_id)->first();
        if($shipping_city){
            $shipping_cost = $shipping_city->shipping_cost;
            $weight = $weight-1;
            if($weight>0){
                $shipping_cost += ceil($weight)*$shipping_city->shipping_cost_extra;
            }
            return ['shipping'=>true, 'shipping_cost'=>$shipping_cost];
        } else {
            $new_shipping_id = 2;
            return ['shipping'=>false, 'shipping_cost'=>0, 'new_shipping_id'=>$new_shipping_id];
        }
    }

    public static function create_sale_payment($payment, $sale, $amount, $detail) {
        $sale_payment = new \Solunes\Store\App\SalePayment;
        $sale_payment->parent_id = $sale->id;
        $sale_payment->payment_id = $payment->id;
        $sale_payment->currency_id = $sale->currency_id;
        $sale_payment->exchange = $sale->exchange;
        $sale_payment->amount = $amount;
        $sale_payment->pending_amount = $amount;
        $sale_payment->detail = $detail;
        $sale_payment->save();
        return $sale_payment;
    }

    public static function check_report_view($view, $array) {
        if(request()->has('download-pdf')){
            $array['pdf'] = true;
            $array['dt'] = 'view';
            $array['header_title'] = 'Reporte generado';
            $array['title'] = 'Reporte generado';
            $array['site'] = \Solunes\Master\App\Site::find(1);
            $pdf = \PDF::loadView($view, $array);
            $header = \View::make('pdf.header', $array);
            return $pdf->setPaper('letter')->setOption('header-html', $header->render())->stream('reporte_'.date('Y-m-d').'.pdf');
        } else {
            return view($view, $array);
        } 
    }

}