@extends('layouts/master')
@include('helpers.meta')

@section('css')
@endsection

@section('header')
<!-- Banner Area Start -->
<div class="banner-area pb-90 pt-160 bg-2">
  <div class="container">
    <div class="row">
      <div class="banner-content text-center text-white">
        <h1>{{ $page->name }}</h1>
        <ul>
          <li><a href="{{ url('') }}">inicio</a> <span class="arrow_carrot-right "></span></li>
          <li>{{ $page->name }}</li>
        </ul> 
      </div>
    </div>
  </div>
</div>
<!-- Banner Area End -->
@endsection

@section('content')
<!-- checkout-area start -->
<div class="checkout-area">
  <div class="container">

    <div class="row">
      <div class="col-lg-6 col-md-6">

        <div class="your-order">
          <h3>Su Orden</h3>
          <div class="your-order-table table-responsive">
            <table>
              <thead>
                <tr>
                  <th class="product-name">Producto</th>
                  <th class="product-total">Total</th>
                </tr>             
              </thead>
              <tbody>
                @if(count($sale->sale_items)>0)
                  @foreach($sale->sale_items as $item)
                    <tr class="cart_item">
                      <td class="product-name">
                        {{ $item->product->name }} <strong class="product-quantity"> X {{ $item->quantity }}</strong>
                      </td>
                      <td class="product-total">
                        <span class="amount">Bs. {{ $item->total_price }}</span>
                      </td>
                    </tr>
                  @endforeach
                @endif
              </tbody>
              <tfoot>
                <tr class="cart-subtotal">
                  <th>Subtotal</th>
                  <td><span class="amount">Bs. {{ $sale->order_amount }}</span></td>
                </tr>
                @foreach($sale->sale_deliveries as $delivery)
                  <tr class="cart-subtotal">
                    <th>Costo de Envío ({{ $delivery->total_weight }} kg.)</th>
                    <td><span class="amount">Bs. {{ $delivery->shipping_cost }}</span></td>
                  </tr>
                @endforeach
                <tr class="order-total">
                  <th>Precio Total</th>
                  <td><strong><span class="amount">Bs. {{ $sale->amount }}</span></strong>
                  </td>
                </tr>               
              </tfoot>
            </table>
          </div>

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
          @include('store::includes.payment-'.$payment->payment->code)
        @endforeach
      </div>  
    </div>
  </div>
</div>
<!-- checkout-area end -->  
@endsection

@section('script')
@endsection