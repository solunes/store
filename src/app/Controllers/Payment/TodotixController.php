<?php

namespace Solunes\Store\App\Controllers\Payment;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class TodotixController extends Controller {

	protected $request;
	protected $url;

	public function __construct() {
	}

  public function getMakePayment($sale_id) {
    $payment = \Solunes\Store\App\Payment::where('code', 'todotix')->first();
    //$sale_id = $request->input('sale_id');
    if($sale_id&&$sale = \Solunes\Store\App\Sale::findId($sale_id)->checkOwner()->status('holding')->first()){

      $user = auth()->user();

      $delivery = $sale->sale_deliveries()->first();

      $final_fields = array(
        "appkey" => 'c26d8c99-8836-4cd5-a850-230c9d3fbf3c',
        "email_cliente" => $user->email,
        "descripcion" => "Pago Compra Online",
        "callback_url" => url('process/sale/'.$sale->id).'/?success=done',
        "razon_social" => $sale->invoice_name,
        "nit" => $sale->invoice_nit,
        "valor_envio" => $delivery->shipping_cost,
        "descripcion_envio" => $delivery->name
      );

      // Generación de items para el carro al detalle de Todotix
      $fields = [];
      foreach($sale->sale_items as $sale_item){
        $sub_field = [];
        $sub_field['concepto'] = $sale_item->product->name;
        $sub_field['cantidad'] = $sale_item->quantity;
        $sub_field['costo_unitario'] = $sale_item->price;
        $fields[] = json_encode($sub_field);
      }

      $final_fields['lineas_detalle_deuda'] = $fields;

      // Consulta CURL a Web Service
      $urlhere = 'http://www.todotix.com:10365/rest/deuda/registrar';
      $ch = curl_init();
      $options = array(
          CURLOPT_URL            => $urlhere,
          CURLOPT_POST           => true,
          CURLOPT_POSTFIELDS     => json_encode($final_fields),
          CURLOPT_RETURNTRANSFER => true,
      );
      curl_setopt_array($ch, $options);
      $result = curl_exec($ch);
      curl_close($ch);  

      $product_result = json_decode($result);

      $transaction_id = $product_result->id_transaccion;
      $api_url = $product_result->url_pasarela_pagos;

      // Generación de Transacción y Redirección
      /*if(count($sale->payment_receipts)>0){
        
      } else {
        $sale_payment = \Store::create_sale_payment($payment, $sale, $sale->amount, 'Detalle');
      }*/

      return redirect($api_url);
    } else {
      return redirect($this->prev)->with('message_error', 'Hubo un error al encontrar su compra.');
    }
  }

}