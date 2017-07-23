{!! Form::open(['url'=>'process/sp-bank-deposit', 'method'=>'post', 'files'=>true]) !!}
  <h3>CONFIRMAR PAGO</h3>
  <div class="coupon-content checkbox-form">           
    <div class="row">
      <div class="col-md-12">
        <p>Una vez realice su transferencia, puede confirmar el pago subiendo su comprobante aquí.</p>
      </div>
      @if(count($sale->payment_receipts)>0)
        <div class="col-md-12">
          <br>
          <p>Ya cargó el siguiente comprobante:</p>
          @foreach($sale->payment_receipts as $payment_receipt)
            <a target="_blank" href="{{ Asset::get_image_path('payment-receipt-image', 'normal', $payment_receipt->image) }}"> 
              {!! Asset::get_image('payment-receipt-image', 'thumb', $payment_receipt->image) !!}
            </a>
          @endforeach
          <br>
        </div>
      @endif
      <div class="col-md-12">
        <div class="checkout-form-list">
          <label>Cargar comprobante de depósito <span class="required">*</span></label>
          {!! Form::file('image', NULL) !!}                   
        </div>
      </div>
      <div class="col-md-12">
        <div class="order-button-payment">
          <input name="sale_id" type="hidden" value="{{ $sale->id }}">
          <input type="submit" value="Enviar Comprobante">
        </div>
      </div>
    </div>   
  </div>
{!! Form::close(); !!}