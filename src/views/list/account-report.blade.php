@extends('master::layouts/admin')

@section('content')
  <h1>Arqueo de Cuentas</h1>

  @include('store::helpers.report.filter')

  @if(count($items)>0)
    <table class="table table-striped table-bordered table-hover dt-responsive">
      <thead>
        <tr class="title">
          <td>Nº</td>
          <td>Cuenta</td>
          <td>Transacción</td>
          <td>Nombre</td>
          <td>Debe</td>
          <td>Haber</td>
          <td>Saldo de Moneda</td>
          <td>Saldo en Bs.</td>
          <td>Fecha</td>
        </tr>
      </thead>
      <tbody>
        <?php $total = 0; ?>
        @foreach($items as $key => $item)
          <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $item->account->name }}</td>
              <td><a href="{{ url('admin/account-book-detail?transaction_code='.$item->transaction_code) }}" target="_blank">{{ $item->transaction_code }}</a></td>
            <td>{{ $item->name }}</td>
            <td>@if($item->debit) {{ $item->debit.' Bs' }} @endif</td>
            <td>@if($item->credit) {{ $item->credit.' Bs' }} @endif</td>
            <td>{{ $item->currency_balance.' '.$item->currency->name }}</td>
            <td>{{ $item->balance.' Bs' }}</td>
            <td>{{ $item->created_at }}</td>
          </tr>
          <?php $total = $total + $item->debit - $item->credit; ?>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="4"></td>
          <td>
            @if($total>0)
              {{ $total.' '.$currency->name }}
            @endif
          </td>
          <td>
            @if($total<0)
              {{ abs($total).' '.$currency->name }}
            @endif
          </td>
          <td colspan="3"></td>
        </tr>
      </tfoot>
    </table><br><br>
    <!--<div class="row">
      <div class="col-sm-6">
        <div id="list-graph-type"></div>
      </div>
    </div>-->
  @else
    <p>No hay registros de ingresos o egresos para las fechas seleccionadas.</p>
  @endif
@endsection
@section('script')
  @include('store::helpers.report.datepicker')
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="http://code.highcharts.com/modules/exporting.js"></script>
  @foreach($graphs as $graph_name => $graph)
    @include('master::scripts.graph-'.$graph["type"].'-js', ['graph_name'=>$graph_name, 'column'=>$graph["name"], 'label'=>$graph["label"], 'graph_items'=>$graph["items"], 'graph_subitems'=>$graph["subitems"], 'graph_field_names'=>$graph["field_names"]])
  @endforeach
@endsection