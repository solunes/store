<div class="row">
  <div class="col-lg-6 col-md-6">

    <div class="order-block">
      <h3>SU ORDEN</h3>
      @include('store::includes.cart-summary', ['items'=>$sale->sale_items, 'order_amount'=>$sale->order_amount, 'deliveries'=>$sale->sale_deliveries, 'total_amount'=>$sale->amount])

    </div>

  </div>  

  <div class="col-lg-6 col-md-6">
    <h3>MÉTODO DE ENVÍO</h3>
    @foreach($sale->sale_deliveries as $delivery)
      <h4>{{ mb_strtoupper($delivery->shipping->name, 'UTF-8') }}</h4>
      <div class="store-form">           
        {!! $delivery->shipping->content !!}
      </div>
    @endforeach
    <h3>MÉTODO DE PAGO</h3>
    @foreach($sale_payments as $payment)
      <h4>{{ mb_strtoupper($payment->payment->name, 'UTF-8') }}</h4>
      <div class="store-form">           
        {!! $payment->payment->content !!}
      </div>
      @include('store::includes.sp-'.$payment->payment->code)
    @endforeach
  </div>  
</div>