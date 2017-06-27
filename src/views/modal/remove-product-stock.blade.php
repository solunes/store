@extends('master::layouts/child-admin')

@section('content')
  <h3>Remover Producto de Stock</h3><h4>Producto: {{ $product->name.' ('.$product->barcode.')' }}</h4>
  <h4>Socio Dueño de Stock: {{ $product->partner->name }}</h4>
  <h4>Cantidad en Stock: {{ $product_stock->quantity }}</h4>
  <h4>Ubicación Actual de Stock: {{ $product_stock->place->name }}</h4>
  {!! Form::open(['url'=>'admin/remove-product-stock', 'method'=>'POST', 'class'=>'form-horizontal filter']) !!}
    <div class="row flex">
      {!! Field::form_input(0, 'edit', ['name'=>'name','type'=>'string','required'=>true], ['cols'=>12,'label'=>'Escriba el motivo para remover del stock']) !!}
      {!! Field::form_input(0, 'edit', ['name'=>'product_stock_id','type'=>'hidden','required'=>true], ['value'=>$product_stock->id]) !!}
    </div>
    {!! Form::submit('Remover Stock de Producto', array('class'=>'btn btn-site')) !!}
  {!! Form::close() !!}
@endsection
@section('script')
  @include('master::scripts.child-ajax-js')
@endsection