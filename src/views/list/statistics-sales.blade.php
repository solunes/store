@extends('master::layouts/admin')

@section('content')
  <h1>Estadísticas de Ventas</h1>

  @include('store::helpers.report.filter')

  <div class="row">
    <div class="col-sm-12">
      <br><br>
      <div id="list-graph-sales"></div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-6">
      <h4 class="center">Ventas por mes en Bs.</h4>
      @if(count($months_array)>0)
        <table class="table table-striped table-bordered table-hover dt-responsive">
          <thead>
            <tr class="title">
              <td>Nombre</td>
              <td>Monto en Bs</td>
            </tr>
          </thead>
          <tbody>
            @foreach($months_array as $name => $amount)
              <tr>
                <td>{{ $name }}</td>
                <td>{{ $amount.' '.$currency->name }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="title">
              <td>Total</td>
              <td>{{ $months_total.' '.$currency->name }}</td>
            </tr>
          </tfoot>
        </table>
      @else
        <p>No hay datos para la tabla.</p>
      @endif
    </div>
    <div class="col-sm-6">
      <h4 class="center">Ventas por departamento en Bs.</h4>
      @if(count($places_array)>0)
        <table class="table table-striped table-bordered table-hover dt-responsive">
          <thead>
            <tr class="title">
              <td>Nombre</td>
              <td>Monto en Bs</td>
            </tr>
          </thead>
          <tbody>
            @foreach($places_array as $name => $amount)
              <tr>
                <td>{{ $name }}</td>
                <td>{{ $amount.' '.$currency->name }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="title">
              <td>Total</td>
              <td>{{ $places_total.' '.$currency->name }}</td>
            </tr>
          </tfoot>
        </table>
      @else
        <p>No hay datos para la tabla.</p>
      @endif
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12">
      <br><br>
      <div id="list-graph-category"></div>
    </div>
  </div>
@endsection
@section('script')
  @include('store::helpers.report.datepicker')
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="http://code.highcharts.com/modules/exporting.js"></script>
  @foreach($graphs as $graph_name => $graph)
    @include('master::scripts.graph-'.$graph["type"].'-js', ['graph_name'=>$graph_name, 'column'=>$graph["name"], 'label'=>$graph["label"], 'graph_items'=>$graph["items"], 'graph_subitems'=>$graph["subitems"], 'graph_field_names'=>$graph["field_names"]])
  @endforeach
@endsection