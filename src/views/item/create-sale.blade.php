@extends('master::layouts/admin')

@section('content')
  <h1>Agregar Productos</h1>
  {!! Form::open(['url'=>'admin/create-sale', 'method'=>'post', 'id'=>'create-sale']) !!}
    <p id="notification-bar"></p>
    <div class="row">
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-barcode"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'barcode', 'required'=>true, 'type'=>'string'], ['label'=>'Introduzca el código de barras o utilce el lector de código de barras', 'cols'=>5]) !!}
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-cart-plus"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'search-product', 'required'=>true, 'type'=>'select', 'options'=>$products], ['label'=>'Seleccione un producto ', 'cols'=>5]) !!}
    </div>
    <table class="table" id="products">
      <thead>
        <tr class="title">
          <td>Nombre Producto</td>
          <td>Precio</td>
          <td>Cantidad</td>
          <td>Subtotal</td>
          <td>X</td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <input type="hidden" name="product_id[]" class="product_id form-control input-lg" />
            <input type="text" name="product_name[]" class="product_name form-control input-lg" readonly />
            <input type="hidden" name="currency[]" class="currency form-control input-lg" />
          </td>
          <td><input type="text" name="price[]" class="price form-control input-lg" /></td>
          <td><input type="text" name="quantity[]" class="quantity form-control input-lg" rel="" /></td>
          <td><input type="text" name="final_price[]" class="final_price form-control input-lg" rel="" readonly /></td>
          <td><a class="delete_row" href="#">X</a></td>
        </tr>
      </tbody>
      <tfoot>
        <tr id="shipping_cost_row">
          <td colspan="3" class="right">Costo de Envío en Bs.</td>
          <td colspan="1">{!! Form::text('shipping_cost', 0, ['class'=>'form-control input-lg', 'id'=>'shipping_cost']) !!}</td>
        </tr>
        <tr>
          <td colspan="3" class="right total">TOTAL</td>
          <td colspan="1">{!! Form::text('amount', 0, ['class'=>'form-control input-lg', 'id'=>'amount', 'readonly'=>true]) !!}</td>
        </tr>
      </tfoot>
    </table>
    <h1>Detalles de Venta</h1>
    <div class="row">
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-money"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'cash_bob', 'required'=>true, 'type'=>'string'], ['value'=>0, 'label'=>'Monto Pagado en Bs.', 'cols'=>3]) !!}
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-money"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'cash_usd', 'required'=>true, 'type'=>'string'], ['value'=>0, 'label'=>'Monto Pagado en USD', 'cols'=>3]) !!}
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-credit-card"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'pos_bob', 'required'=>true, 'type'=>'string'], ['value'=>0, 'label'=>'Monto Pagado en Bs. por POS', 'cols'=>3]) !!}
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-calculator"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'paid_amount', 'required'=>true, 'type'=>'string'], ['value'=>0, 'label'=>'Monto Total Pagado en Bs.', 'cols'=>3], ['readonly'=>true]) !!}
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-balance-scale"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'change', 'required'=>true, 'type'=>'string'], ['value'=>0, 'label'=>'Cambio a Devolver en Bs.', 'cols'=>3], ['readonly'=>true]) !!}
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-dollar"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'exchange', 'required'=>true, 'type'=>'string'], ['value'=>round($currency_dollar->main_exchange, 2), 'label'=>'Tipo de Cambio de USD', 'cols'=>3]) !!}
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-building"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'place_id', 'required'=>true, 'type'=>'select', 'options'=>$places], ['label'=>'Seleccione la Tienda', 'cols'=>3]) !!}
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-tags"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'type', 'required'=>true, 'type'=>'select', 'options'=>$types], ['label'=>'Tipo de Venta', 'cols'=>3]) !!}
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-calendar"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'credit', 'required'=>true, 'type'=>'select', 'options'=>[0=>'Venta al Contado', 1=>'Venta a Crédito']], ['value'=>0, 'label'=>'¿Venta a crédito?', 'cols'=>3]) !!}
    </div>
    <div id="field_credit_amount" class="row">
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-paper-plane"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'credit_amount', 'required'=>true, 'type'=>'string'], ['value'=>0, 'label'=>'Monto por Cobrar en Bs.', 'cols'=>3], ['readonly'=>true]) !!}
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-calendar-check-o"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'credit_due', 'required'=>true, 'type'=>'date'], ['label'=>'Fecha de Vencimiento de Crédito', 'cols'=>3, 'class'=>'datepicker']) !!}
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-list-alt"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'credit_details', 'required'=>true, 'type'=>'string'], ['label'=>'Detalles de Crédito', 'cols'=>3]) !!}
    </div>
    <div class="row">
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-file-text-o"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'invoice', 'required'=>true, 'type'=>'select', 'options'=>$invoices], ['value'=>1, 'label'=>'¿Incluye factura?', 'cols'=>3]) !!}
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-terminal"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'invoice_nit', 'required'=>true, 'type'=>'string'], ['label'=>'NIT de Cliente', 'cols'=>3]) !!}
      <div class="col-sm-1 hidden-xs icon"><i class="fa fa-user"></i></div>
      {!! Field::form_input($i, $dt, ['name'=>'invoice_name', 'required'=>true, 'type'=>'string'], ['label'=>'Nombre de Cliente', 'cols'=>3]) !!}
    </div>
    {!! Form::hidden('action_form', $action) !!}
    {!! Form::hidden('model_node', $model) !!}
    {!! Form::hidden('lang_code', \App::getLocale()) !!}
    {!! Form::submit('Finalizar Venta', array('class'=>'btn btn-site')) !!}

  {!! Form::close() !!}
@endsection
@section('script')
  @include('master::scripts.select-js')
  @include('store::scripts.barcode-sale-js')
@endsection