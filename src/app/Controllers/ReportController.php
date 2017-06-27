<?php

namespace Solunes\Store\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Asset;

class ReportController extends Controller {

	protected $request;
	protected $url;

	public function __construct(UrlGenerator $url) {
	  $this->prev = $url->previous();
	}


  public function getSalesReport() {
    $model = \Solunes\Store\App\Sale::whereNotNull('id');
    $array = \Store::check_report_header($model);
    $array['show_place'] = true;

    $concept_array = ['income_sale', 'income_sale_credit', 'expense_refund'];
    $concept_array = \Solunes\Store\App\Concept::whereIn('code', $concept_array)->lists('id')->toArray();
    $account_array = \Solunes\Store\App\Account::whereIn('concept_id', $concept_array)->lists('id')->toArray();
    $accounts = \Solunes\Store\App\PlaceAccountability::whereIn('account_id', $account_array)->where('created_at', '>=', $array['i_date'])->where('created_at', '<=', $array['e_date']);
    if($array['place']!='all'){
      $accounts = $accounts->where('place_id', $array['place']);
    }
    $accounts = $accounts->get();
    $store = 0;
    $cash = 0;
    $pos = 0;
    $web = 0;
    $online = 0;
    $pending_total = 0;
    $sales_total = 0;
    $refund_total = 0;
    foreach($accounts as $item){
      $new_total = \Store::calculate_currency($item->amount, $array['currency'], $item->currency);
      if($item->account->code=='expense_refund'){
        $refund_total -= $new_total;
      } else if($item->account->code=='income_sale_credit') {
        $pending_total += $new_total;
      } else {
        $store += $new_total;
        $sales_total += $new_total;
        foreach($item->other_accounts as $other){
          $other_amount = \Store::calculate_currency($other->amount, $array['currency'], $other->currency);
          if($other->account->concept->code=='asset_cash'){
            $cash += $other_amount;
          } else if($other->account->concept->code=='asset_bank'){
            $pos += $other_amount;
          }
        }
        /*if($item->type=='normal'){
          $store += $paid;
          if($item->pos_bob>0){
            $new_total -= $item->pos_bob;
            $paid -= $item->pos_bob;
            $pos += $item->pos_bob;
          } 
          $cash += $paid;
        } else if($item->type=='web'){
          $web += $paid;
        } else if($item->type=='online'){
          $online += $paid;
        }*/
      }
    }
    $array = $array + ['total'=>$sales_total, 'store'=>$store, 'cash'=>$cash,'pos'=>$pos, 'web'=>$web, 'online'=>$online, 'pending'=>$pending_total, 'refund_total'=>$refund_total];
    // Gráficos
    $type_items = [['type'=>'paid','total'=>round($store)], ['type'=>'web','total'=>round($web)], ['type'=>'online','total'=>round($online)], ['type'=>'pending','total'=>round($pending_total)]];
    $type_items = json_decode(json_encode($type_items));
    $type_field_names = ['paid'=>'Ventas en Tienda '.$array['currency']->name, 'web'=>'Ventas Web '.$array['currency']->name, 'online'=>'Ventas Online '.$array['currency']->name, 'pending'=>'Ventas no Cobradas '.$array['currency']->name];
    $array['graphs']['type'] = ['type'=>'pie', 'graph_name'=>'type', 'name'=>'type', 'label'=>'Tipo de Ventas', 'items'=>$type_items, 'subitems'=>[], 'field_names'=>$type_field_names];
    return \Store::check_report_view('store::list.sales-report', $array);
  }

  public function getSalesDetailReport() {
    $model = \Solunes\Store\App\Sale::whereNotNull('id');
    $array = \Store::check_report_header($model, ['web'=>'Web', 'online'=>'Online', 'pos'=>'POS']);
    $array['show_place'] = true;

    $sales = \Solunes\Store\App\Sale::where('created_at', '>=', $array['i_date'])->where('created_at', '<=', $array['e_date']);
    if($array['place']!='all'){
      if($array['place']=='web'){
        $sales = $sales->where('type', 'web');
      } else if($array['place']=='online'){
        $sales = $sales->where('type', 'online');
      } else if($array['place']=='pos'){
        $sales = $sales->where('type', 'pos');
      } else {
        $sales = $sales->where('place_id', $array['place']);
      }
    }
    $sales = $sales->with('sale_items')->get();
    $array_items = [];
    $paid = 0;
    $pending = 0;
    $shipping = 0;
    $count = 1;
    foreach($sales as $sale){
      $new_total = \Store::calculate_currency($sale->amount, $array['currency'], $sale->currency);
      if($sale->shipping_cost>0){
        $shipping_cost = \Store::calculate_currency($sale->shipping_cost, $array['currency'], $sale->currency);
        $shipping += $shipping_cost;
        $new_total -= $shipping_cost;
      }
      if($pending_payment = $sale->pending_payment){
        $new_pending = \Store::calculate_currency($pending_payment->amount, $array['currency'], $pending_payment->currency);
        $pending_amount = \Store::calculate_currency($pending_payment->amount, $sale->currency, $pending_payment->currency);
        $paid += ($new_total - $new_pending);
        $pending += $new_pending;
      } else {
        $pending_amount = 0;
        $paid += $new_total;
      }
      foreach($sale->sale_items as $item){
        $subtotal = round($item->price * $item->quantity);
        $array_items[$item->id] = ['count'=>$count++, 'sale'=>$sale, 'item'=>$item, 'total'=>$subtotal];
        $subpending = 0;
        if($pending_amount>0){
          if($subtotal>$pending_amount){
            $subpending = $pending_amount;
            $pending_amount = 0;
          } else {
            $subpending = $subtotal;
            $pending_amount -= $subtotal;
          }
        }
        $array_items[$item->id]['pending'] = number_format($subpending, 2, '.', '').' '.$item->currency->name;
      }
    }
    $array['pending'] = $pending;
    $array['paid'] = $paid;
    $array['shipping'] = $shipping;
    $array['total'] = $pending + $paid;
    $array['items'] = $array_items;
    return \Store::check_report_view('store::list.sales-detail-report', $array);
  }

  public function getProductsReport() {
    $model = \Solunes\Store\App\ProductStock::whereNotNull('id');
    $array = \Store::check_report_header($model);
    $array['show_place'] = true;

    $products = \Solunes\Store\App\ProductStock::where('created_at', '>=', $array['i_date'])->where('created_at', '<=', $array['e_date']);
    if($array['place']!='all'){
        $products = $products->where('place_id', $array['place'])->orderBy('place_id', 'ASC');
    }
    $products = $products->where('quantity', '>', 0)->with('parent')->get();
    $total = 0;
    foreach($products as $product){
      $amount = $product->parent->cost * $product->quantity;
      $new_total = \Store::calculate_currency($amount, $array['currency'], $product->parent->currency);
      $total += $new_total;
    }
    $array['items'] = $products;
    $array['total'] = $total;
    return \Store::check_report_view('store::list.products-report', $array);
  }

  public function getPendingPaymentsReport($type) {
    if($type=='accounts-receivable'){
      $model = \Solunes\Store\App\AccountsReceivable::whereNotNull('id');
    } else if($type=='accounts-payable'){
      $model = \Solunes\Store\App\AccountsPayable::whereNotNull('id');
    }
    $array = \Store::check_report_header($model);

    $pending_payments = $model->where('created_at', '>=', $array['i_date'])->where('created_at', '<=', $array['e_date'])->with('place')->get();
    $paid = 0;
    $pending = 0;
    foreach($pending_payments as $item){
      $new_total = \Store::calculate_currency($item->amount, $array['currency'], $item->currency);
      if($item->status=='paid'){
        $paid += $new_total;
      } else {
        $pending += $new_total;
      }
    }
    $array['type'] = $type;
    $array['pending'] = $pending;
    $array['paid'] = $paid;
    $array['total'] = $pending + $paid;
    $array['items'] = $pending_payments;
    if($type=='accounts-receivable'){
      $array['title'] = 'Reporte de Cuentas por Cobrar';
    } else {
      $array['title'] = 'Reporte de Cuentas por Pagar';
    }
    return \Store::check_report_view('store::list.pending-payments-report', $array);
  }

  public function getPartnersReport() {
    $model = \Solunes\Store\App\PartnerMovement::whereNotNull('id');
    $array = \Store::check_report_header($model);

    $partner_movements = \Solunes\Store\App\PartnerMovement::where('created_at', '>=', $array['i_date'])->where('created_at', '<=', $array['e_date'])->get();
    $partner_details = \Solunes\Store\App\PurchaseProduct::where('created_at', '>=', $array['i_date'])->where('created_at', '<=', $array['e_date'])->get();
    $debit = 0;
    $credit = 0;
    foreach($partner_movements as $item){
      $new_total = \Store::calculate_currency($item->amount, $array['currency'], $item->currency);
      if($item->type=='debit'){
        $debit += $new_total;
      } else {
        $credit += $new_total;
      }
    }
    $array['debit'] = $debit;
    $array['credit'] = $credit;
    $array['total'] = $credit - $debit;
    $array['items'] = $partner_movements;
    $array['partner_details'] = $partner_details;
    return \Store::check_report_view('store::list.partner-report', $array);
  }

  public function getAccountBookReport() {
    $model = \Solunes\Store\App\PlaceAccountability::whereNotNull('id');
    $array = \Store::check_report_header($model);
    $array['show_place'] = true;
    unset($array['places']['all']);

    $place_id = $array['place'];
    if($place_id=='all'){
      $place_id = auth()->user()->place_id;
    }

    $accounts = \Solunes\Store\App\Account::get();
    $array['items'] = [];
    $place = \Solunes\Store\App\Place::find($place_id);
    foreach($accounts as $account){
      $items = $place->place_accountability();
      $array['items'][$account->id] = ['name'=>$account->name, 'items'=>$items->where('account_id', $account->id)->where('created_at', '>=', $array['i_date'])->where('created_at', '<=',$array['e_date'])->with('currency')->orderBy('id','ASC')->get()];
    }
    return \Store::check_report_view('store::list.account-book-report', $array);
  }

  public function getAccountBookDetailReport() {
    $model = \Solunes\Store\App\PlaceAccountability::whereNotNull('id');
    $array = \Store::check_report_header($model, [], 'account_id');
    $array['show_place'] = true;
    unset($array['places']['all']);

    $place_id = $array['place'];
    if($place_id=='all'){
      $place_id = auth()->user()->place_id;
    }

    //$concept_array = \App\Concept::where('type', 'transference')->lists('id')->toArray();
    $place = \Solunes\Store\App\Place::find($place_id);
    $items = $place->place_accountability();
    if(request()->has('transaction_code')){
      $items = $items->where('transaction_code', request()->input('transaction_code'));
    } else {
      $items = $items->where('account_id', $array['current_account_id'])->where('created_at', '>=', $array['i_date'])->where('created_at', '<=',$array['e_date']);
    }
    $items = $items->with('currency')->get();
    $array['items'] = $items;
    $first = 0;
    $last = 0;
    $balance = 0;
    $new_balance = 0;
    foreach($array['items'] as $key => $item){
      $new_balance = \Store::calculate_currency($item->balance, $array['currency'], $item->currency);
      if($key==0){
        $new_amount = \Store::calculate_currency($item->real_amount, $array['currency'], $item->currency);
        $first = $new_balance - $new_amount;
      }
    }
    $last = $new_balance;
    $balance = $last-$first;
    $array = $array + ['first'=>$first, 'last'=>$last, 'balance'=>$balance];
    // Gráficos
    $type_items = [['type'=>'first','total'=>round($first)], ['type'=>'last','total'=>round($last)]];
    $type_items = json_decode(json_encode($type_items));
    $type_field_names = ['first'=>'Balance inical en '.$array['currency']->name, 'last'=>'Balance final en '.$array['currency']->name];
    $array['graphs']['type'] = ['type'=>'bar', 'graph_name'=>'type', 'name'=>'type', 'label'=>'Cambio de Capital', 'items'=>$type_items, 'subitems'=>[], 'field_names'=>$type_field_names];
    return \Store::check_report_view('store::list.account-report', $array);
  }

  public function getAccountIncomeStatement() {
    $model = \Solunes\Store\App\PlaceAccountability::whereNotNull('id');
    $array = \Store::check_report_header($model);
    $array['show_place'] = true;
    unset($array['places']['all']);

    $place_id = $array['place'];
    if($place_id=='all'){
      $place_id = auth()->user()->place_id;
      $array['place'] = $place_id;
    }

    //$array_items = ['sales'=>['income'=>['total'=>0],'expense'=>['total'=>0]], 'operations'=>['income'=>['total'=>0],'expense'=>['total'=>0]], 'other'=>['expense'=>['total'=>0]], 'iue'=>['expense'=>['total'=>0]], 'inflation'=>['expense'=>['total'=>0]]];
    $concepts = \Solunes\Store\App\Concept::whereIn('type', ['income','expense'])->with('account')->get();
    foreach($concepts as $concept){
      $array_items[$concept->name]['total'] = 0;
      foreach($concept->accounts as $account){
        $items = \Solunes\Store\App\PlaceAccountability::where('parent_id', $place_id)->where('account_id', $account->id)->where('created_at', '>=', $array['i_date'])->where('created_at', '<=',$array['e_date'])->with('currency')->get();
        $total = 0;
        foreach($items as $item){
          $amount = $item->amount;
          if($item->type=='debit'){
            $amount = -$amount;
          }
          $new_amount = \Store::calculate_currency($amount, $array['currency'], $item->currency);
          $total += $new_amount;
        }
        $total = round($total, 2);
        $array_items[$concept->name][$account->name] = ['total'=>$total, 'id'=>$account->id];
        if(isset($array_items[$concept->name]['total'])){
          $array_items[$concept->name]['total'] += $total;
        } else {
          $array_items[$concept->name]['total'] = $total;
        }
      }
    }
    $array['items'] = $array_items;
    $array['sales'] = $array_items['Ingresos por Ventas']['total'];
    $array['sales_cost'] = $array_items['Costo de Venta']['total'];
    $array['brute_profit'] = $array['sales'] + $array['sales_cost'];
    $array['operations_costs'] = $array_items['Gasto Operativo']['total'];
    $array['operations_profit'] = $array['brute_profit'] + $array['operations_costs'];
    $array['before_tax_profit'] = $array['operations_profit'] + $array_items['Otro Ingreso']['total'] + $array_items['Otro Gasto']['total'];
    $array['after_tax_profit'] = $array['before_tax_profit'] + $array_items['Impuestos IUE']['total'];
    $array['profit'] = $array['after_tax_profit'] + $array_items['Ajuste por Inflación']['total'];
    // Gráficos
    /*$type_items = [['type'=>'income','total'=>round(100)], ['type'=>'expense','total'=>round(200)]];
    $type_items = json_decode(json_encode($type_items));
    $type_field_names = ['income'=>'Ingreso en '.$array['currency']->name, 'expense'=>'Egreso en '.$array['currency']->name];
    $array['graphs']['type'] = ['type'=>'pie', 'graph_name'=>'type', 'name'=>'type', 'label'=>'Ingresos/Egresos', 'items'=>$type_items, 'subitems'=>[], 'field_names'=>$type_field_names];*/
    return \Store::check_report_view('store::list.account-income-statement', $array);
  }

  public function getBalanceSheet() {
    $model = \Solunes\Store\App\PlaceAccountability::whereNotNull('id');
    $array = \Store::check_report_header($model);
    $array['show_place'] = true;
    unset($array['places']['all']);

    $place_id = $array['place'];
    if($place_id=='all'){
      $place_id = auth()->user()->place_id;
      $array['place'] = $place_id;
    }

    //$array_items = ['sales'=>['income'=>['total'=>0],'expense'=>['total'=>0]], 'operations'=>['income'=>['total'=>0],'expense'=>['total'=>0]], 'other'=>['expense'=>['total'=>0]], 'iue'=>['expense'=>['total'=>0]], 'inflation'=>['expense'=>['total'=>0]]];
    $concepts = \Solunes\Store\App\Concept::with('account')->get();
    $total_profit = 0;
    foreach($concepts as $concept){
      foreach($concept->accounts as $account){
        $items = \Solunes\Store\App\PlaceAccountability::where('parent_id', $place_id)->where('account_id', $account->id)->where('created_at', '>=', $array['i_date'])->where('created_at', '<=',$array['e_date'])->with('currency')->get();
        $total = 0;
        foreach($items as $key => $item){
          if($key==0){
            $amount = $item->balance;
          } else {
            $amount = $item->real_amount;
          }
          $new_amount = \Store::calculate_currency($amount, $array['currency'], $item->currency);
          $total += $new_amount;
        }
        if($concept->type!='asset'){
          $total = -$total;
        }
        $total = round($total, 2);
        if($concept->type=='income'||$concept->type=='expense'){
          $total_profit += $total;
        } else {
          $array_items[$concept->type][$concept->name][$account->name] = $total;
        }
      }
    }
    $array_items['equity']['Capital']['Utilidad de Gestión'] = $total_profit;
    $array['items'] = $array_items;
    // Gráficos
    /*$type_items = [['type'=>'income','total'=>round(100)], ['type'=>'expense','total'=>round(200)]];
    $type_items = json_decode(json_encode($type_items));
    $type_field_names = ['income'=>'Ingreso en '.$array['currency']->name, 'expense'=>'Egreso en '.$array['currency']->name];
    $array['graphs']['type'] = ['type'=>'pie', 'graph_name'=>'type', 'name'=>'type', 'label'=>'Ingresos/Egresos', 'items'=>$type_items, 'subitems'=>[], 'field_names'=>$type_field_names];*/
    return \Store::check_report_view('store::list.balance-sheet', $array);
  }

  public function getStatisticsSales() {
    $model = \Solunes\Store\App\Sale::whereNotNull('id');
    $array = \Store::check_report_header($model);

    $sales = \Solunes\Store\App\Sale::where('created_at', '>=', $array['i_date'])->where('created_at', '<=', $array['e_date'])->get();  
    
    // Category Sales
    $categories = \Solunes\Store\App\Category::lists('name', 'id')->toArray();
    foreach($categories as $category_key => $category_name){
      $categories_array[$category_key] = 0;
    }

    // Months
    $months = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
    foreach($months as $month_key => $month_name){
      $months_array[$month_name] = 0;
    }
    foreach($months as $month_key => $month_name){
      $months_profit_array[$month_name] = 0;
    }
    $months_total = 0;
    // Places
    $places = \Solunes\Store\App\Place::where('type','store')->lists('name', 'id')->toArray() + ['web'=>'Web', 'online'=>'Online'];
    foreach($places as $place_key => $place_name){
      $places_array[$place_name] = 0;
    }
    $places_total = 0;
    foreach($sales as $item){
      // Calcular monto total de la venta
      $new_total = \Store::calculate_currency($item->amount, $array['currency'], $item->currency);
      
      // Months
      $name = $months[$item->created_at->format('m')];
      $months_array[$name] += $new_total;
      $months_profit_array[$name] += $new_total;
      $months_total += $new_total;

      // Places
      if($item->type=='web'){
        $name = 'Web';
      } else if($item->type=='online'){
        $name = 'Online';
      } else {
        $name = $item->place->name;
      }
      $places_array[$name] += $new_total;
      $places_total += $new_total;

      // Category
      foreach($item->sale_items as $sale_item){
        $new_sale_item = \Store::calculate_currency($sale_item->total, $array['currency'], $sale_item->currency);
        $categories_array[$sale_item->product->category_id] += $new_sale_item;
      }
    }
    $array['months_array'] = $months_array;
    $array['months_total'] = $months_total;
    $array['places_array'] = $places_array;
    $array['places_total'] = $places_total;
    // Gráfico de Ventas
    $type_items = [];
    $type_field_names = [];
    foreach($months as $month_key => $month_name){
      $type_items[] = ['type'=>$month_key, 'total'=>round($months_array[$month_name])];
      $type_field_names[$month_key] = $month_name;
    }
    $type_items = json_decode(json_encode($type_items));
    $array['graphs']['sales'] = ['type'=>'bar', 'graph_name'=>'sales', 'name'=>'type', 'label'=>'Ventas', 'items'=>$type_items, 'subitems'=>[], 'field_names'=>$type_field_names];
    // Gráfico de Composición de Ventas
    $type_items = [];
    $type_field_names = [];
    foreach($categories as $category_key => $category_name){
      if($categories_array[$category_key]>0){
        $type_items[] = ['type'=>$category_key, 'total'=>round($categories_array[$category_key])];
        $type_field_names[$category_key] = $category_name;
      }
    }
    $type_items = json_decode(json_encode($type_items));
    $array['graphs']['category'] = ['type'=>'pie', 'graph_name'=>'category', 'name'=>'type', 'label'=>'Composicion de Ventas', 'items'=>$type_items, 'subitems'=>[], 'field_names'=>$type_field_names];
    return \Store::check_report_view('store::list.statistics-sales', $array);
  }

}