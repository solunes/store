<?php

namespace Solunes\Store\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

use Validator;
use Asset;
use AdminList;
use AdminItem;
use PDF;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProcessController extends Controller {

	protected $request;
	protected $url;

	public function __construct(UrlGenerator $url) {
	  $this->prev = $url->previous();
	}

  public function getCalculateShipping($shipping_id, $city_id, $weight) {
    $shipping_array = \Store::calculate_shipping_cost($shipping_id, $city_id, $weight);
    return $shipping_array;
  }

  public function getAddCartItem($product_id) {
    if($product = \Solunes\Store\App\Product::find($product_id)){
      $cart = \Store::get_cart();
      \Store::add_cart_item($cart, $product, 1);
      return redirect($this->prev)->with('message_success', 'Se añadió su producto al carro de compras.');
    } else {
      return redirect($this->prev)->with('message_error', 'Debe seleccionar un producto existente.');
    }
  }

  public function getDeleteCartItem($cart_item_id) {
    if($cart_item = \Solunes\Store\App\CartItem::find($cart_item_id)){
      $cart_item->delete();
    }
    return redirect($this->prev);
  }

  public function postAddCartItem(Request $request) {
    if($product = \Solunes\Store\App\Product::find($request->input('product_id'))){
      if($request->input('quantity')>0){
        $cart = \Store::get_cart();
        \Store::add_cart_item($cart, $product, $request->input('quantity'));
        return redirect($this->prev)->with('message_success', 'Se añadió su producto al carro de compras.');
      } else {
        return redirect($this->prev)->with('message_error', 'Debe seleccionar una cantidad positiva.');
      }
    } else {
      return redirect($this->prev)->with('message_error', 'Hubo un error al agregar el producto, intente nuevamente.');
    }
  }

  public function getCheckCart($type) {
    if($cart = \Solunes\Store\App\Cart::checkOwner()->checkCart()->status('holding')->first()){
      $page = \Solunes\Master\App\Page::find(2);
      $view = 'process.confirmar-compra';
      if(!view()->exists($view)){
        $view = 'store::'.$view;
      }
      $total = 0;
      foreach($cart->cart_items as $cart_item){
        $total += $cart_item->total_price;
      }
      return view($view, ['cart'=>$cart, 'page'=>$page, 'total'=>$total]);
    } else {
      return redirect('')->with('message_error', 'No se encontró un carro de compras abierto en su sesión.');
    }
  }

  public function postUpdateCart(Request $request) {
    if($cart = \Solunes\Store\App\Cart::checkOwner()->checkCart()->status('holding')->first()){
      $cart->touch();
      foreach($cart->cart_items as $item){
        if(isset($request->input('product_id')[$item->id])&&$request->input('quantity')[$item->id]>0){
          $item->quantity = $request->input('quantity')[$item->id];
          $item->save();
        } else {
          $item->delete();
        }
      }
      return redirect($this->prev)->with('message_success', 'Se actualizó su carro de compras correctamente.');
    } else {
      return redirect($this->prev)->with('message_error', 'Hubo un error al actualizar su carro de compras.');
    }
  }

  public function getBuyNow($slug) {
    if($item = \Solunes\Store\App\Product::findBySlug($slug)){
      $page = \Solunes\Master\App\Page::find(2);
      $view = 'process.comprar-ahora';
      if(!view()->exists($view)){
        $view = 'store::'.$view;
      }
      return view($view, ['product'=>$item, 'page'=>$page]);
    } else {
      return redirect('')->with('message_error', 'No se encuentra el producto para ser comprado.');
    }
  }

  public function postBuyNow(Request $request) {
    $validator = \Validator::make($request->all(), \Solunes\Store\App\Cart::$rules_send);
    if(!$validator->passes()){
      return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos obligatorios.')->withErrors($validator)->withInput();
    } else if($request->input('quantity')>0&&$product = \Solunes\Store\App\Product::find($request->input('product_id'))){
      $cart = new \Solunes\Store\App\Cart;
      if(\Auth::check()){
        $cart->user_id = \Auth::user()->id;
      }
      $cart->session_id = \Session::getId();
      $cart->type = 'buy-now';
      $cart->save();

      $cart_item = new \Solunes\Store\App\CartItem;
      $cart_item->parent_id = $cart->id;
      $cart_item->product_id = $product->id;
      $cart_item->quantity = $request->input('quantity');
      $cart_item->price = $product->real_price;
      $cart_item->weight = $product->weight;
      $cart_item->save();

      return redirect('process/finalizar-compra/'.$cart->id)->with('message_success', 'Ahora puede confirmar los datos de su pedido.');
    } else {
      return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos obligatorios.')->withErrors($validator)->withInput();
    }
  }

  public function getFinishSale($cart_id = NULL) {
    if(($cart_id&&$cart = \Solunes\Store\App\Cart::findId($cart_id)->checkBuyNow()->checkOwner()->status('holding')->first())||($cart = \Solunes\Store\App\Cart::checkOwner()->checkCart()->status('holding')->first())){
      if(\Auth::check()){
        $user = \Auth::user();
        $array['auth'] = true;
        $array['city_id'] = 1;
        $array['address'] = $user->address;
        $array['address_extra'] = $user->address_extra;
      } else {
        session()->set('url.intended', url()->current());
        $array['auth'] = false;
        $array['city_id'] = 1;
        $array['address'] = NULL;
        $array['address_extra'] = NULL;
      }
      $array['cart'] = $cart;
      $array['cities'] = \Solunes\Store\App\City::lists('name','id');
      $array['shipping_options'] = \Solunes\Store\App\Shipping::active()->order()->lists('name','id');
      $array['shipping_descriptions'] = \Solunes\Store\App\Shipping::active()->order()->get();
      $array['payment_options'] = \Solunes\Store\App\Payment::active()->order()->lists('name','id');
      $array['payment_descriptions'] = \Solunes\Store\App\Payment::active()->order()->get();
      $array['page'] = \Solunes\Master\App\Page::find(2);
      $total = 0;
      $weight = 0;
      foreach($cart->cart_items as $cart_item){
        $total += $cart_item->total_price;
        $weight += $cart_item->total_weight;
      }
      $array['total'] = $total;
      $array['weight'] = $weight;
      $view = 'process.finalizar-compra';
      if(!view()->exists($view)){
        $view = 'store::'.$view;
      }
      return view($view, $array);
    } else {
      return redirect('')->with('message_error', 'No se encuentra el producto para ser comprado.');
    }
  }

  public function postFinishSale(Request $request) {
    $cart_id = $request->input('cart_id');
    if(auth()->check()){
      $rules = \Solunes\Store\App\Sale::$rules_auth_send;
    } else {
      $rules = \Solunes\Store\App\Sale::$rules_send;
    }
    $validator = \Validator::make($request->all(), $rules);
    if(!$validator->passes()){
      return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos obligatorios.')->withErrors($validator)->withInput();
    } else if($cart_id&&$cart = \Solunes\Store\App\Cart::findId($cart_id)->checkOwner()->status('holding')->first()){
      $new_user = false;

      $order_cost = 0;
      $order_weight = 0;
      foreach($cart->cart_items as $item){
        $order_cost += $item->total_price;
        $order_weight += $item->total_weight;
      }
      $shipping_array = \Store::calculate_shipping_cost($request->input('shipping_id'), $request->input('city_id'), $order_weight);
      if($shipping_array['shipping']===false){
        return redirect($this->prev)->with('message_error', 'No se encontró el método de envío para esta ciudad, seleccione otro.')->withInput();
      }
      $shipping_cost = $shipping_array['shipping_cost'];

      // User
      if(\Auth::check()) {
        $user = \Auth::user();
      } else {
        $new_user = true;
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $user = new \App\User;
        $user->name = $first_name.' '.$last_name;
        $user->email = $request->input('email');
        $user->cellphone = $request->input('cellphone');
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->password = $request->input('password');
      }
      $user->city_id = $request->input('city_id');
      $user->address = $request->input('address');
      $user->address_extra = $request->input('address_extra');
      $user->save();
      $member = \Solunes\Master\App\Role::where('name', 'member')->first();
      $user->role_user()->sync([$member->id]);
      
      // Sale
      $total_cost = $order_cost + $shipping_cost;
      $place = \Solunes\Store\App\Place::find(1); // Parametrizar tienda en config
      $currency = \Solunes\Store\App\Currency::find(1); // Parametrizar tienda en config
      $sale = new \Solunes\Store\App\Sale;
      $sale->user_id = $user->id;
      $sale->place_id = $place->id;
      $sale->currency_id = $currency->id;
      $sale->order_amount = $order_cost;
      $sale->amount = $total_cost;
      $sale->invoice = true;
      $sale->type = 'online';
      $sale->save();

      // Sale Payment
      $sale_payment = new \Solunes\Store\App\SalePayment;
      $sale_payment->parent_id = $sale->id;
      $sale_payment->payment_id = $request->input('payment_id');
      $sale_payment->currency_id = $currency->id;
      $sale_payment->exchange = $currency->main_exchange;
      $sale_payment->amount = $total_cost;
      $sale_payment->pending_amount = $total_cost;
      $sale_payment->detail = 'Pago por compra online';
      $sale_payment->save();

      // Sale Delivery
      $sale_delivery = new \Solunes\Store\App\SaleDelivery;
      $sale_delivery->parent_id = $sale->id;
      $sale_delivery->shipping_id = $request->input('shipping_id');
      $sale_delivery->currency_id = $sale->currency_id;
      $sale_delivery->city_id = $request->input('city_id');
      if($request->has('city_other')){
        $sale_delivery->city_other = $request->input('city_other');
      }
      $sale_delivery->name = 'Pedido de venta en linea';
      $sale_delivery->address = $request->input('address');
      $sale_delivery->address_extra = $request->input('address_extra');
      $sale_delivery->total_weight = $order_weight;
      $sale_delivery->shipping_cost = $shipping_cost;
      $sale_delivery->save();

      // Sale Items
      foreach($cart->cart_items as $cart_item){
        $sale_item = new \Solunes\Store\App\SaleItem;
        $sale_item->parent_id = $sale->id;
        $sale_item->product_id = $cart_item->product_id;
        $sale_item->currency_id = $currency->id;
        $sale_item->price = $cart_item->price;
        $sale_item->quantity = $cart_item->quantity;
        $sale_item->weight = $cart_item->weight;
        $sale_item->save();
      }

      $cart->status = 'sale';
      $cart->user_id = $user->id;
      $cart->save();

      if($new_user){
        $user->role_user()->attach(2);
        \Auth::loginUsingId($user->id);
      }

      // Send Email
      $vars = ['@name@'=>$user->name, '@total_cost@'=>$sale->total_cost, '@sale_link@'=>url('process/sale/'.$sale->id)];
      \FuncNode::make_email('new-sale', [$user->email], $vars);

      return redirect('process/sale/'.$sale->id)->with('message_success', 'Su compra fue confirmada correctamente, ahora debe proceder al pago para finalizarla.');
    } else {
      return redirect($this->prev)->with('message_error', 'Hubo un error al actualizar su carro de compras.');
    }
  }

  public function getSale($sale_id) {
    if($sale = \Solunes\Store\App\Sale::findId($sale_id)->checkOwner()->with('cart','cart.cart_items')->first()){
      $array['page'] = \Solunes\Master\App\Page::find(2);
      $array['sale'] = $sale;
      $array['sale_payments'] = $sale->sale_payments;
      $view = 'process.sale';
      if(!view()->exists($view)){
        $view = 'store::'.$view;
      }
      return view($view, $array);
    } else {
      return redirect($this->prev)->with('message_error', 'Hubo un error al encontrar su compra.');
    }
  }


  public function getPaidSale($encrypted_sale_id) {
    \Log::info('test_payment_1: '.$encrypted_sale_id);
    $sale_id = urldecode(\Crypt::decrypt($encrypted_sale_id));
    \Log::info('test_payment_2: '.$sale_id);
    if($sale = \Solunes\Store\App\Sale::find($sale_id)){
      $sale->paid_amount = $sale->amount;
      /*if($sale->invoice){
        $sale->invoice_name = request()->input('invoice_name');
        $sale->invoice_nit = request()->input('invoice_nit');
      }*/
      $sale->status = 'paid';
      $sale->save();
      return redirect('inicio#alert')->with('message_success', 'Su pago fue procesado correctamente, nos contactarémos con usted cuando realicemos el envío.');
    } else {
      return redirect('inicio#alert')->with('message_error', 'Hubo un error al encontrar su pago.');
    }
  }

  public function postSpBankDeposit(Request $request) {
    $sale_id = $request->input('sale_id');
    $validator = \Validator::make($request->all(), \Solunes\Store\App\SpBankDeposit::$rules_send);
    if(!$validator->passes()){
      return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos obligatorios.')->withErrors($validator)->withInput();
    } else if($sale_id&&$sale = \Solunes\Store\App\Sale::findId($sale_id)->checkOwner()->status('holding')->first()){
      if(count($sale->payment_receipts)>0){
        $payment_receipt = $sale->payment_receipts->first();
      } else {
        $payment_receipt = new \Solunes\Store\App\SpBankDeposit;
        $payment_receipt->sale_id = $sale->id;
        $payment_receipt->sale_payment_id = $sale->sale_payments()->first()->id;
        $payment_receipt->status = 'holding';
      }
      $payment_receipt->image = \Asset::upload_image($request->file('image'), 'sp-bank-deposit-image');
      $payment_receipt->save();

      return redirect($this->prev)->with('message_success', 'Su pago fue recibido, sin embargo aún debe ser confirmado por nuestros administradores.');
    } else {
      return redirect($this->prev)->with('message_error', 'Hubo un error al encontrar su compra.');
    }
  }

}