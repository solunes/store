@extends('master::layouts/admin')

@section('content')
  <h1>Reporte de Socios</h1>

  @include('store::helpers.report.filter')

  <h3>Detalle de Capital</h3>
  @if(count($partner_details)>0)
    <table class="table table-striped table-bordered table-hover dt-responsive">
      <thead>
        <tr class="title">
          <td>Nº</td>
          <td>Estado</td>
          <td>Socio</td>
          <td>Socio (Transporte)</td>
          <td>Producto</td>
          <td>Fecha de Compra</td>
          <td>Última Actualización</td>
          <td>Inversión</td>
          <td>Inversión (Transporte)</td>
          <td>Retorno</td>
          <td>Retorno (Transporte)</td>
          <td>Ganancia</td>
          <td>Inv. Actual</td>
          <td>Ver</td>
        </tr>
      </thead>
      <tbody>
        @foreach($partner_details as $item_key => $item)
          <tr>
            <td>{{ $item_key + 1 }}</td>
            <td>{{ trans('master::admin.'.$item->status) }}</td>
            <td>{{ $item->partner->name }}</td>
            <td>{{ $item->partner_transport->name }}</td>
            <td>{{ $item->product->name }}</td>
            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $item->updated_at->format('d/m/Y H:i') }}</td>
            <td>{{ $item->investment.' '.$item->currency->name }}</td>
            <td>{{ $item->transport_investment.' '.$item->currency->name }}</td>
            <td>{{ $item->return.' '.$item->currency->name }}</td>
            <td>{{ $item->transport_return.' '.$item->currency->name }}</td>
            <td>{{ $item->profit.' '.$item->currency->name }}</td>
            <td>{{ $item->quantity }}</td>
            <td><a target="_blank" href="{{ url('admin/model/partner-movement/edit/'.$item->id) }}">Ver</a></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <p>No hay registros de ingresos o egresos para las fechas seleccionadas.</p>
  @endif

  <h3>Detalle de Movimientos</h3>
  @if(count($items)>0)
    <table class="table table-striped table-bordered table-hover dt-responsive">
      <thead>
        <tr class="title">
          <td>Nº</td>
          <td>Socio</td>
          <td>Detalle</td>
          <td>Fecha</td>
          <td>Total</td>
          <td>Ver</td>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item_key => $item)
          <tr class=" @if($item->type=='credit') edit @elseif($item->type=='debit') delete @endif ">
            <td>{{ $item_key + 1 }}</td>
            <td>{{ $item->parent->name }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $item->amount.' '.$item->currency->name }}</td>
            <td><a target="_blank" href="{{ url('admin/model/partner-movement/edit/'.$item->id) }}">Ver</a></td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="title">
          <td colspan="2">Ingresos de Capital: {{ round($credit, 2).' '.$currency->name }}</td>
          <td colspan="1">Salidas de Capital: {{ round($debit, 2).' '.$currency->name }}</td>
          <td colspan="2">Balance de Capital: {{ round($total, 2).' '.$currency->name }}</td>
        </tr>
      </tfoot>
    </table>
  @else
    <p>No hay registros de ingresos o egresos para las fechas seleccionadas.</p>
  @endif
@endsection
@section('script')
  @include('store::helpers.report.datepicker')
@endsection