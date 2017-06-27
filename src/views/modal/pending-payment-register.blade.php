@extends('master::layouts/child-admin')

@section('content')
  <h3>Registrar Pago</h3><h4>Pago Pendiente: {{ $pending_payment->name }}<br>Detalle: {{ $pending_payment->detail }}</h4>
  <h4>Pago Pendiente: {{ $pending_amount.' '.$pending_payment->currency->real_name }}</strong> / 
  Pago Realizado: {{ $paid_amount.' '.$pending_payment->currency->real_name }}</strong></h4>
  {!! Form::open(['url'=>'admin/modal-pending-payment-register', 'method'=>'POST', 'class'=>'form-horizontal filter']) !!}
    <div class="row flex">
      {!! Field::form_input(0, 'edit', ['name'=>'currency_id','type'=>'select','required'=>true, 'options'=>$currencies], ['value'=>$pending_payment->currency_id, 'cols'=>6,'label'=>'Moneda'], ['readonly'=>true]) !!}
      {!! Field::form_input(0, 'edit', ['name'=>'amount','type'=>'string','required'=>true], ['value'=>$pending_amount, 'cols'=>6,'label'=>'Pago a Registrar'], ['data-amount'=>$pending_amount]) !!}
      {!! Field::form_input(0, 'edit', ['name'=>'type','type'=>'hidden','required'=>false], ['value'=>$type]) !!}
      {!! Field::form_input(0, 'edit', ['name'=>'pending_payment_id','type'=>'hidden','required'=>false], ['value'=>$pending_payment->id]) !!}
    </div>
    {!! Form::submit('Registrar Pago', array('class'=>'btn btn-site')) !!}
  {!! Form::close() !!}
@endsection
@section('script')
  @include('master::scripts.child-ajax-js')
  @include('store::scripts.pending-payment-register-js')
@endsection