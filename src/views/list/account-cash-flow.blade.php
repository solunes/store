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
        <tr class="title">
          <td>Ingresos por Operaciones</td>
          <td>{{ $items['operations']['income']['total'].' '.$currency->name }}</td>
        </tr>
        @foreach($items['operations']['income'] as $concept_name => $concept_total)
          @if($concept_name!='total')
            <tr>
              <td>{{ $concept_name }}</td>
              <td>{{ $concept_total.' '.$currency->name }}</td>
            </tr>
          @endif
        @endforeach
        <tr class="title">
          <td>Egresos por Operaciones</td>
          <td>{{ $items['operations']['expense']['total'].' '.$currency->name }}</td>
        </tr>
        @foreach($items['operations']['expense'] as $concept_name => $concept_total)
          @if($concept_name!='total')
            <tr>
              <td>{{ $concept_name }}</td>
              <td>{{ $concept_total.' '.$currency->name }}</td>
            </tr>
          @endif
        @endforeach
        <tr class="title">
          <td>Ingresos por Capital</td>
          <td>{{ $items['capital']['income']['total'].' '.$currency->name }}</td>
        </tr>
        @foreach($items['capital']['income'] as $concept_name => $concept_total)
          @if($concept_name!='total')
            <tr>
              <td>{{ $concept_name }}</td>
              <td>{{ $concept_total.' '.$currency->name }}</td>
            </tr>
          @endif
        @endforeach
        <tr class="title">
          <td>Egresos por Capital</td>
          <td>{{ $items['capital']['expense']['total'].' '.$currency->name }}</td>
        </tr>
        @foreach($items['capital']['expense'] as $concept_name => $concept_total)
          @if($concept_name!='total')
            <tr>
              <td>{{ $concept_name }}</td>
              <td>{{ $concept_total.' '.$currency->name }}</td>
            </tr>
          @endif
        @endforeach
        <tr class="title">
          <td>CAMBIO DE EFECTIVO TOTAL</td>
          <td>{{ $items['operations']['income']['total']-$items['operations']['expense']['total']+$items['capital']['income']['total']-$items['capital']['expense']['total'].' '.$currency->name }}</td>
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
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="http://code.highcharts.com/modules/exporting.js"></script>
  @foreach($graphs as $graph_name => $graph)
    @include('master::scripts.graph-'.$graph["type"].'-js', ['graph_name'=>$graph_name, 'column'=>$graph["name"], 'label'=>$graph["label"], 'graph_items'=>$graph["items"], 'graph_subitems'=>$graph["subitems"], 'graph_field_names'=>$graph["field_names"]])
  @endforeach
@endsection