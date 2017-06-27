@extends('layouts/master')
@include('store::helpers.meta')

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
<div class="container">
  <div class="row">
     <div class="col-md-12 col-sm-12 col-xs-12">
      <form action="{{ url('process/update-cart') }}" method="post">       
        <div class="table-content table-responsive">
          <table>
            <thead>
              <tr>
                <th class="product-thumbnail">Imagen</th>
                <th class="product-name">Producto</th>
                <th class="product-price">Precio</th>
                <th class="product-quantity">Cantidad</th>
                <th class="product-subtotal">Total</th>
                <th class="product-remove">Remover</th>
              </tr>
            </thead>
            <tbody>
              <?php $total = 0; ?>
              @foreach($cart->cart_items as $item)
                <tr>
                  <td class="product-thumbnail"><a target="_blank" href="{{ url('product/'.$item->product->slug) }}">
                    {!! Asset::get_image('product-image', 'cart', $item->product->image) !!}
                  </a></td>
                  <td class="product-name"><a target="_blank" href="{{ url('product/'.$item->product->slug) }}">{{ $item->product->name }}</a></td>
                  <td class="product-price"><span class="amount">Bs. {{ $item->price }}</span></td>
                  <td class="product-quantity">
                    <input name="quantity[{{ $item->id }}]" type="number" value="{{ $item->quantity }}">
                    <input name="product_id[{{ $item->id }}]" type="hidden" value="{{ $item->id }}">
                  </td>
                  <td class="product-subtotal">Bs. {{ $item->total_price }}</td>
                  <td class="product-remove"><a href="#" class="delete"><i class="fa fa-times"></i></a></td>
                </tr>
                <?php $total += $item->total_price; ?>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-md-9 col-sm-7 col-xs-12">
            <div class="buttons-cart">
              <input type="submit" value="Actualizar Carro">
              <a href="{{ url('productos') }}">Seguir comprando</a>
            </div>
          </div>
          <div class="col-md-3 col-sm-5 col-xs-12">
            <div class="cart_totals">
              <!--<h2>TOTAL</h2>-->
              <table>
                <tbody>
                  <!--<tr class="cart-subtotal">
                    <th>Subtotal</th>
                    <td><span class="amount">Bs. 1000</span></td>
                  </tr>-->
                  <tr class="order-total">
                    <th>Total</th>
                    <td>
                      <strong><span class="amount">Bs. {{ $total }}</span></strong>
                    </td>
                  </tr>                     
                </tbody>
              </table>
              <div class="wc-proceed-to-checkout">
                <a href="{{ url('process/finalizar-compra') }}">Confirmar Compra</a>
              </div>
            </div>
          </div>
        </div>
      </form> 
    </div>
  </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
  $('a.delete').click(function (event) {
    $(this).parents('tr').first().remove();
    event.stopPropagation();
    return false;
  });
</script>
@endsection