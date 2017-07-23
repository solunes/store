<div class="row">
  <div class="col-lg-6 col-md-6">

    <div class="your-order">
      <h3>Su Orden</h3>
      @include('store::includes.cart-summary', ['items'=>$sale->sale_items, 'order_amount'=>$sale->order_amount, 'deliveries'=>$sale->sale_deliveries, 'total_amount'=>$sale->amount])

    </div>

  </div>  

  <div class="col-lg-6 col-md-6">
    <h1>MÉTODO DE ENVÍO</h1>
    @foreach($sale->sale_deliveries as $delivery)
      <h3>{{ mb_strtoupper($delivery->shipping->name, 'UTF-8') }}</h3>
      <div class="coupon-content checkbox-form">           
        <div class="row">
          <div class="col-md-12">
            {!! $delivery->shipping->content !!}
          </div>
        </div>
      </div>
    @endforeach
    <h1>MÉTODO DE PAGO</h1>
    @foreach($sale_payments as $payment)
      <h3>{{ mb_strtoupper($payment->payment->name, 'UTF-8') }}</h3>
      <div class="coupon-content checkbox-form">           
        <div class="row">
          <div class="col-md-12">
            {!! $payment->payment->content !!}
          </div>
        </div>
      </div>
      @include('store::includes.sp-'.$payment->payment->code)
    @endforeach
  </div>  
</div>