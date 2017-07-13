<?php

namespace Solunes\Store\App\Controllers\Payment;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class BankDepositController extends Controller {

	protected $request;
	protected $url;

	public function __construct() {
	}

  public function postMakePayment(Request $request) {
    $payment = \Solunes\Store\App\Payment::where('code', 'bank_deposit')->first();
    $sale_id = $request->input('sale_id');
    $validator = \Validator::make($request->all(), \Solunes\Store\App\PaymentBankDeposit::$rules_send);
    if(!$validator->passes()){
      return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos obligatorios.')->withErrors($validator)->withInput();
    } else if($sale_id&&$sale = \Solunes\Store\App\Sale::findId($sale_id)->checkOwner()->status('holding')->first()){
      if(count($sale->payment_receipts)>0){
        $bank_deposit = $sale->sale_payments()->where('payment_id', $payment->id)->first();
      } else {
        $sale_payment = \Store::create_sale_payment($payment, $sale, $sale->amount, 'Detalle');
        $bank_deposit = new \Solunes\Store\App\PaymentBankDeposit;
        $bank_deposit->sale_id = $sale->id;
        $bank_deposit->sale_payment_id = $sale_payment->id;
      }
      $bank_deposit->image = \Asset::upload_image($request->file('image'), 'payment-bank-deposit-image');
      $bank_deposit->save();

      return redirect($this->prev)->with('message_success', 'Su pago fue recibido, sin embargo aún debe ser confirmado por nuestros administradores.');
    } else {
      return redirect($this->prev)->with('message_error', 'Hubo un error al encontrar su compra.');
    }
  }

}