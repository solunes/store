@extends('master::layouts/admin')

@section('content')
  <h1>Contabilidad: Estado de Resultados</h1>

  @include('store::helpers.report.filter')

  @if(count($items)>0)
    <table class="table table-striped table-bordered table-hover dt-responsive">
      <thead>
        <tr class="title">
          <td>Concepto</td>
          <td>Monto</td>
        </tr>
      </thead>
      <tbody>
        @include('store::includes.account-income-sub', ['items'=>$items['Ingresos por Ventas']])
        <tr class="title">
          <td>TOTAL DE VENTAS</td>
          <td>{{ $sales.' '.$currency->name }}</td>
        </tr>
        @include('store::includes.account-income-sub', ['items'=>$items['Costo de Venta']])
        <tr class="title">
          <td>TOTAL DE COSTO DE VENTAS</td>
          <td>{{ $sales_cost.' '.$currency->name }}</td>
        </tr>
        <tr class="title">
          <td>UTILIDAD BRUTA</td>
          <td>{{ $brute_profit.' '.$currency->name }}</td>
        </tr>
        @include('store::includes.account-income-sub', ['items'=>$items['Gasto Operativo']])
        <tr class="title">
          <td>TOTAL GASTO OPERACION</td>
          <td>{{ $operations_costs .' '.$currency->name }}</td>
        </tr>
        <tr class="title">
          <td>UT NETA DE OPERACION  </td>
          <td>{{ $operations_profit .' '.$currency->name }}</td>
        </tr>
        @include('store::includes.account-income-sub', ['items'=>$items['Otro Ingreso']])
        @include('store::includes.account-income-sub', ['items'=>$items['Otro Gasto']])
        <tr class="title">
          <td>UT ANT IMPUESTOS</td>
          <td>{{ $before_tax_profit.' '.$currency->name }}</td>
        </tr>
        @include('store::includes.account-income-sub', ['items'=>$items['Impuestos IUE']])
        <tr class="title">
          <td>UTILIDAD ANTES DE PARTIDAS EXTRAORDINARIAS</td>
          <td>{{ $after_tax_profit.' '.$currency->name }}</td>
        </tr>
        @include('store::includes.account-income-sub', ['items'=>$items['Ajuste por Inflación']])
        <tr class="title">
          <td>UTILIDAD NETA</td>
          <td>{{ $profit.' '.$currency->name }}</td>
        </tr>
      </tbody>
    </table>
    <div class="row">
      <div class="col-sm-6">
        <div id="list-graph-operations"></div>
      </div>
      <div class="col-sm-6">
        <div id="list-graph-capital"></div>
      </div>
    </div>
  @else
    <p>No hay registros de ingresos o egresos para las fechas seleccionadas.</p>
  @endif
@endsection
@section('script')
  @include('store::helpers.report.datepicker')
@endsection