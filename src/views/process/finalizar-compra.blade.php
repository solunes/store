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
                <?php $total = 0; ?>
                <?php $weight = 0; ?>
                @if(count($cart->cart_items)>0)
                  @foreach($cart->cart_items as $item)
                    <tr class="cart_item">
                      <td class="product-name">
                        {{ $item->product->name }} <strong class="product-quantity"> X {{ $item->quantity }}</strong>
                      </td>
                      <td class="product-total">
                        <span class="amount">Bs. {{ $item->total_price }}</span>
                      </td>
                    </tr>
                    <?php $total += $item->total_price; ?>
                    <?php $weight += $item->total_weight; ?>
                  @endforeach
                @endif
              </tbody>
              <tfoot>
                <tr class="cart-subtotal">
                  <th>Subtotal</th>
                  <td>Bs. <span class="amount">{{ $total }}</span></td>
                </tr>
                <tr class="cart-subtotal">
                  <th>Costo de Envío ({{ round($weight, 1) }} kg.)</th>
                  <td>Bs. <span class="amount shipping_cost">0</span></td>
                </tr>
                <tr class="order-total">
                  <th>Precio Total</th>
                  <td><strong>Bs. <span class="amount total_cost">{{ $total }}</span></strong>
                  </td>
                </tr>               
              </tfoot>
            </table>
          </div>

          @if(count($shipping_descriptions)>0)
            <h4 class="method-title">Métodos de Envío</h4>
            <div class="payment-method">
              <div class="payment-accordion">
                <div class="panel-group" id="accordion-shipping" role="tablist" aria-multiselectable="true">
                  @foreach($shipping_descriptions as $key => $shipping)
                    <div class="panel panel-default">
                      <div class="panel-heading" role="tab" id="heading{{ $key }}"><h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion-shipping" href="#collapse-shipping-{{ $key }}" aria-expanded="true" aria-controls="collapse{{ $key }}">{{ $shipping->name }}</a>
                      </h4></div>
                      <div id="collapse-shipping-{{ $key }}" class="panel-collapse collapse @if($key==0) in @endif " role="tabpanel" aria-labelledby="heading{{ $key }}">
                        <div class="panel-body">{!! $shipping->content !!}</div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          @endif

          @if(count($payment_descriptions)>0)
            <h4 class="method-title">Métodos de Pago</h4>
            <div class="payment-method">
              <div class="payment-accordion">
                <div class="panel-group" id="accordion-payment" role="tablist" aria-multiselectable="true">
                  @foreach($payment_descriptions as $key => $payment)
                    <div class="panel panel-default">
                      <div class="panel-heading" role="tab" id="heading{{ $key }}"><h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#accordion-payment" href="#collapse-payment-{{ $key }}" aria-expanded="true" aria-controls="collapse{{ $key }}">{{ $payment->name }}</a>
                      </h4></div>
                      <div id="collapse-payment-{{ $key }}" class="panel-collapse collapse @if($key==0) in @endif " role="tabpanel" aria-labelledby="heading{{ $key }}">
                        <div class="panel-body">{!! $payment->content !!}</div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          @endif

        </div>

      </div>  
      <div class="col-lg-6 col-md-6">
        @if(!$auth)
          <h3>INICIAR SESIÓN</h3>
          <div class="coupon-content">
            <div class="coupon-info">
              <p class="coupon-text">Si ya tiene una cuenta de usuario, inicie sesión con su usuario y contraseña. Si no recuerda su contraseña, puede <a href="{{ url('') }}">recuperarla aquí</a>.</p>
              <form action="{{ url('auth/login') }}" method="post">
                <?php request()->session()->put('url.intended', request()->url()); ?>
                <p class="form-row-first">
                  <label>Email o Celular <span class="required">*</span></label>
                  {!! Form::text('user', NULL) !!}
                </p>
                <p class="form-row-last">
                  <label>Contraseña  <span class="required">*</span></label>
                  {!! Form::password('password', NULL) !!}
                </p>
                <p class="form-row">          
                  <input type="submit" value="Iniciar Sesión">
                  <label>
                    {!! Form::checkbox('remember', NULL) !!}
                     Recordarme 
                  </label>
                </p>
              </form>
            </div>
          </div>
        @endif
        <form action="{{ url('process/finish-sale') }}" method="post">
          @if(!$auth)
            <h3>REGISTRO DE CLIENTE</h3>
          @else
            <h3>DATOS DE ENVÍO</h3>
          @endif
          <div class="coupon-content checkbox-form">           
            <div class="row">
              <div class="col-md-12">
                <div class="country-select">
                  <label>Ciudad <span class="required">*</span></label>
                  {!! Form::select('city_id', $cities, $city_id, ['id'=>'city_id', 'class'=>'query_shipping']) !!}                   
                </div>
              </div>
              <div class="col-md-12 city_other">
                <div class="checkout-form-list">
                  <label>Especifique la Otra Ciudad</label>                   
                  {!! Form::text('city_other', NULL) !!}
                </div>
              </div>
              @if(!$auth)
                <div class="col-md-6">
                  <div class="checkout-form-list">
                    <label>Nombre <span class="required">*</span></label>                   
                    {!! Form::text('first_name', NULL) !!}
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="checkout-form-list">
                    <label>Apellido <span class="required">*</span></label>                    
                    {!! Form::text('last_name', NULL) !!}
                  </div>
                </div>
              @endif
              <div class="col-md-12">
                <div class="checkout-form-list">
                  <label>Dirección <span class="required">*</span></label>
                  {!! Form::text('address', $address, ['placeholder'=>'Datos de zona, barrio, calle, número']) !!}
                </div>
              </div>
              <div class="col-md-12">
                <div class="checkout-form-list">                  
                  {!! Form::text('address_extra', $address_extra, ['placeholder'=>'Otros detalles como color, referencias, etc. (Opcional)']) !!}
                </div>
              </div>
              @if(!$auth)
                <div class="col-md-6">
                  <div class="checkout-form-list">
                    <label>Email <span class="required">*</span></label>                    
                    {!! Form::text('email', NULL, ['placeholder'=>'Introduzca un correo electrónico']) !!}
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="checkout-form-list">
                    <label>Teléfono / Celular <span class="required">*</span></label>                   
                    {!! Form::text('cellphone', NULL, ['placeholder'=>'Teléfono o celular']) !!}
                  </div>
                </div>
              @endif
              <div class="col-md-6">
                <div class="checkout-form-list">
                  <label>Método de Envío <span class="required">*</span></label>                   
                  {!! Form::select('shipping_id', $shipping_options, NULL, ['id'=>'shipping_id', 'class'=>'query_shipping']) !!}
                </div>
              </div>
              <div class="col-md-6">
                <div class="checkout-form-list">
                  <label>Método de Pago <span class="required">*</span></label>                    
                  {!! Form::select('payment_id', $payment_options, NULL, ['id'=>'payment_id']) !!}
                </div>
              </div>
              @if(!$auth)
                <div class="col-md-12">
                  <div class="checkout-form-list">
                    <p>Para facilitar sus compras a futuro, introduzca una contraseña y así se guardarán sus datos en una cuenta de usuario.</p>
                    <label>Contraseña <span class="required">*</span></label>
                    <input name="password" type="password" placeholder="Contraseña">  
                  </div>
                </div>  
              @endif
              <div class="col-md-12">
                <div class="order-button-payment">
                  <input name="cart_id" type="hidden" value="{{ $cart->id }}">
                  <input type="submit" value="Finalizar Compra">
                </div>
              </div>
            </div>   
          </div>
        </form>                   
      </div>  
    </div>
  </div>
</div>
<!-- checkout-area end -->  
@endsection

@section('script')
<script type="text/javascript">
  function queryShipping(){
    var order_cost = {{ $total }};
    var weight = {{ $weight }};
    var shipping_id = $('#shipping_id').val();
    var city_id = $('#city_id').val();
    $.ajax("{{ url('process/calculate-shipping') }}/" + shipping_id + "/" + city_id + "/" + weight, {
      success: function(data) {
        if(data.shipping){
          var shipping_cost = parseFloat(data.shipping_cost);
          var total_cost = order_cost + shipping_cost;
          $(".shipping_cost").html(shipping_cost);
          $(".total_cost").html(total_cost);
        } else {
          var shipping_id = $('#shipping_id').val(data.new_shipping_id);
          queryShipping();
          alert('No se puede realizar un envío a esa ciudad por ese método de envío. Por lo tanto, le cambiamos a Unibol Courier.');
        }
      }
    });
  }

  function updateOtherCity(){
    var city_id = $('#city_id').val();
    console.log('City ID: ' + city_id)
    if(city_id==11){
      $('.city_other').fadeIn();
    } else {
      $('.city_other').fadeOut();
    }
  }

  $( document ).ready(function() {
    queryShipping();
    updateOtherCity();
  });

  $(document).on('change', 'select.query_shipping', function() {
    queryShipping();
  });

  $(document).on('change', 'select#city_id', function() {
    updateOtherCity();
  });

</script>
@endsection