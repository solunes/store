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
        "appkey" => config('store.pagostt_code'),
        "email_cliente" => $user->email,
        "descripcion" => "Pago Compra Online",
        "callback_url" => url('process/paid-sale/'.urlencode(\Crypt::encrypt($sale->id))),
        "razon_social" => $sale->invoice_name,
        "nit" => $sale->invoice_nit,
        "valor_envio" => $delivery->shipping_cost,
        "emite_factura" => $sale->invoice,
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

      // Decodificar resultado
      $decoded_result = json_decode($result);
      
      if(!isset($decoded_result->url_pasarela_pagos)){
        \Log::info('Error Resultado: '.json_encode($decoded_result));
        return redirect('inicio')->with('message_error', 'Hubo un error al procesar su compra.');
      }

      $transaction_id = $decoded_result->id_transaccion;
      $api_url = $decoded_result->url_pasarela_pagos;

      return redirect($api_url);
    } else {
      return redirect('inicio')->with('message_error', 'Hubo un error al encontrar su compra.');
    }
  }

}