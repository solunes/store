@extends('master::layouts/child-admin')

@section('content')
  <h3>Añadir Compra</h3><h4>Producto: {{ $product->name.' ('.$product->barcode.')' }}</h4>
  <h5>Socio: <strong>{{ $product->partner->name }}</strong> Precio sin factura: <strong>{{ $product->price.' '.$product->currency->real_name }}</strong> / Precio con factura: <strong>{{ $product->invoice_price.' '.$product->currency->real_name }}</strong></h5>
  {!! Form::open(['url'=>'admin/modal-purchase-add-product', 'method'=>'POST', 'class'=>'form-horizontal filter']) !!}
    <div class="row flex">
      {!! Field::form_input(0, 'edit', ['name'=>'currency_id','type'=>'select','required'=>true, 'options'=>$currencies], ['value'=>$product->currency_id, 'cols'=>6,'label'=>'Moneda'], ['readonly'=>true]) !!}
      {!! Field::form_input(0, 'edit', ['name'=>'parcial_cost','type'=>'string','required'=>true], ['value'=>$product->cost, 'cols'=>6,'label'=>'Costo Unitario']) !!}
      {!! Field::form_input(0, 'edit', ['name'=>'shipping_cost','type'=>'string','required'=>true], ['value'=>0, 'cols'=>6,'label'=>'Costo de Transporte Unitario']) !!}
      {!! Field::form_input(0, 'edit', ['name'=>'quantity','type'=>'string','required'=>true], ['value'=>1, 'cols'=>6,'label'=>'Cantidad de Compra']) !!}
      {!! Field::form_input(0, 'edit', ['name'=>'cost','type'=>'string','required'=>true], ['value'=>$product->cost, 'cols'=>6,'label'=>'Costo Unitario Ajustado'], ['readonly'=>true]) !!}
      {!! Field::form_input(0, 'edit', ['name'=>'subtotal','type'=>'string','required'=>true], ['value'=>$product->cost, 'cols'=>6, 'label'=>'Subtotal en Bs.'], ['readonly'=>true]) !!}
      {!! Field::form_input(0, 'edit', ['name'=>'parent_id','type'=>'hidden','required'=>false], ['value'=>$parent_id]) !!}
      {!! Field::form_input(0, 'edit', ['name'=>'product_id','type'=>'hidden','required'=>true], ['value'=>$product->id]) !!}
    </div>
    {!! Form::submit('Agregar Lote de Producto', array('class'=>'btn btn-site')) !!}
  {!! Form::close() !!}
@endsection
@section('script')
  @include('master::scripts.child-ajax-js')
  @include('store::scripts.purchase-add-js')
@endsection