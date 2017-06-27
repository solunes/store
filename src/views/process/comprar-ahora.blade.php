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
      <form action="{{ url('process/buy-now') }}" method="post">       
        <div class="table-content table-responsive">
          <table>
            <thead>
              <tr>
                <th class="product-thumbnail">Imagen</th>
                <th class="product-name">Producto</th>
                <th class="product-price">Precio Unitario</th>
                <th class="product-quantity">Cantidad</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="product-thumbnail"><a target="_blank" href="{{ url('product/'.$product->slug) }}">
                  {!! Asset::get_image('product-image', 'subdetail', $product->image) !!}
                </a></td>
                <td class="product-name"><a target="_blank" href="{{ url('product/'.$product->slug) }}">{{ $product->name }}</a></td>
                <td class="product-price"><span class="amount">Bs. {{ $product->price }}</span></td>
                <td class="product-quantity">
                  <input name="quantity" type="number" value="1">
                  <input name="product_id" type="hidden" value="{{ $product->id }}">
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-md-9 col-sm-7 col-xs-12">
            <div class="buttons-cart">
              <input type="submit" value="Confirmar Compra">
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