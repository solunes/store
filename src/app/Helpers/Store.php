<?php 

namespace Solunes\Store\App\Helpers;

use Validator;

class Store {

    public static function after_seed() {
        $node_array['currency'] = ['action_field'=>['edit']];
        $node_array['place'] = ['action_field'=>['edit']];
        $node_array['tax'] = ['action_field'=>['edit']];
        $node_array['transaction-code'] = ['action_field'=>['edit']];
        $node_array['concept'] = ['action_field'=>['edit']];
        $node_array['account'] = ['action_field'=>['edit']];
        $node_array['bank-account'] = ['action_field'=>['edit']];
        $node_array['user'] = ['action_field'=>['edit']];
        $node_array['income'] = ['action_field'=>['edit']];
        $node_array['expense'] = ['action_field'=>['edit']];
        $node_array['inventory-movement'] = ['action_field'=>['edit']];
        $node_array['accounts-receivable'] = ['action_field'=>['edit']];
        $node_array['accounts-payable'] = ['action_field'=>['edit']];
        $node_array['product'] = ['action_field'=>['edit']];
        $node_array['product-stock'] = ['action_field'=>['edit']];
        $node_array['partner'] = ['action_field'=>['edit']];
        $node_array['partner-movement'] = ['action_field'=>['view']];
        $node_array['sale'] = ['action_field'=>['view'], 'action_node'=>['back','excel']];
        //$node_array['account'] = ['action_field'=>['view']];
        //$node_array['account-detail'] = ['action_field'=>['view']];
        //$node_array['account-movement'] = ['action_field'=>['view']];
        //$node_array['income-result'] = ['action_field'=>['view'], 'action_node'=>['back','excel']];
        foreach($node_array as $node_name => $node_detail){
            $node = \Solunes\Master\App\Node::where('name', $node_name)->first();
            foreach($node_detail as $extra_type => $extra_value) {
                $node_extra = new \Solunes\Master\App\NodeExtra;
                $node_extra->parent_id = $node->id;
                $node_extra->type = $extra_type;
                $node_extra->value_array = json_encode($extra_value);
                $node_extra->save();
            }
        }
        // Borrar opciones del menú
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 1)->whereTranslation('name', 'Global')->delete();
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 1)->whereTranslation('name', 'Sitio')->delete();
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 2)->whereTranslation('name', 'Ventas')->delete();
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 2)->whereTranslation('name', 'Devoluciones')->delete();
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 2)->whereTranslation('name', 'Grupos de Productos')->delete();
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 2)->whereTranslation('name', 'Detalles de Resultados')->delete();
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 2)->whereTranslation('name', 'Ordenes de Compras')->delete();
        \Solunes\Master\App\Menu::where('menu_type', 'admin')->where('level', 2)->whereTranslation('name', 'Variaciones')->delete();

        // Menú
        $pm = \Solunes\Master\App\Menu::where('menu_type', 'admin')->whereTranslation('name', 'Contabilidad')->first();
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'pencil','name'=>'Crear Ingreso','link'=>'admin/model/income/create'];
        //$menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'th-list','name'=>'Reporte de Ingresos','link'=>'admin/account-cash-flow/credit'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'pencil','name'=>'Crear Egreso','link'=>'admin/model/expense/create'];
        //$menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'th-list','name'=>'Reporte de Egresos','link'=>'admin/account-cash-flow/debit'];
        $pm = \Solunes\Master\App\Menu::where('menu_type', 'admin')->whereTranslation('name', 'Compañia')->first();
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'th-list','name'=>'Usuarios','link'=>'admin/model-list/user'];
        $pm = \Solunes\Master\App\Menu::where('menu_type', 'admin')->whereTranslation('name', 'Ventas')->first();
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'calculator','name'=>'Realizar Venta','link'=>'admin/create-sale'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'calculator','name'=>'Realizar Devolución','link'=>'admin/create-refund'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'th-list','name'=>'Reporte Diario','link'=>'admin/sales-report?period=day&initial_date=&initial_date_submit=&end_date=&end_date_submit=&currency_id=1&place_id=all'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'th-list','name'=>'Reporte de Ventas','link'=>'admin/sales-detail-report'];
        $pm = \Solunes\Master\App\Menu::where('menu_type', 'admin')->whereTranslation('name', 'Inventario')->first();
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'pencil','name'=>'Agregar Producto','link'=>'admin/model/product/create'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'th-list','name'=>'Reporte de Inventario','link'=>'admin/products-report'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'print','name'=>'Imprimir Etiquetas','link'=>'admin/generate-barcodes-pdf'];
        $pm = new \Solunes\Master\App\Menu;
        $pm->level = 1;
        $pm->type = 'blank';
        $pm->menu_type = 'admin';
        $pm->icon = 'area-chart';
        $pm->name = 'Reportes';
        $pm->save();
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Resumen de Ventas','link'=>'admin/sales-report'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Detalle de Ventas','link'=>'admin/sales-detail-report'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Reporte de Productos','link'=>'admin/products-report'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Cuentas por Pagar','link'=>'admin/pending-payments-report/accounts-payable'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Cuentas por Cobrar','link'=>'admin/pending-payments-report/accounts-receivable'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Aportes de Socios','link'=>'admin/partners-report'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Estado de Resultados','link'=>'admin/account-income-statement'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Balance General','link'=>'admin/balance-sheet'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Arqueo de Cuentas','link'=>'admin/account-book-detail'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Libro Mayor','link'=>'admin/account-book'];
        $menu_array[] = ['parent_id'=>$pm->id,'level'=>2,'icon'=>'bar-chart','name'=>'Estadísticas de Ventas','link'=>'admin/statistics-sales'];
        foreach($menu_array as $new_menu){
            $menu = new \Solunes\Master\App\Menu;
            if(isset($new_menu['parent_id'])){
                $menu->parent_id = $new_menu['parent_id'];
            }
            $menu->level = $new_menu['level'];
            $menu->menu_type = 'admin';
            $menu->icon = $new_menu['icon'];
            $menu->name = $new_menu['name'];
            $menu->link = $new_menu['link'];
            if(isset($new_menu['order'])){
                $menu->order = $new_menu['order'];
            }
            $menu->save();
        }
        return 'After seed de store realizado correctamente.';
    }

    public static function check_category_children($category, $category_array = []) {
        $category_array[] = $category->id;
        if(count($category->children)>0){
          foreach($category->children as $subcategory){
            $category_array = \Store::check_category_children($subcategory, $category_array);
          }
        }
        return $category_array;
    }

    public static function calculate_currency($item_amount, $main_currency, $item_currency) {
        if($main_currency->id!=$item_currency->id){
            if($main_currency->type!='main'){
                $item_amount = $item_amount / $item_currency->main_exchange;
            } else {
                $item_amount = $item_amount * $item_currency->main_exchange;
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

    public static function register_account($place_id, $type, $account_id, $currency_id, $amount, $name) {
        return ['place_id'=>$place_id, 'type'=>$type, 'account_id'=>$account_id, 'currency_id'=>$currency_id, 'amount'=>$amount, 'name'=>$name];
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