@extends('master::layouts/admin')

@section('content')
  <h1>{{ $title }}</h1>

  @include('store::helpers.report.filter')

  @if(count($items)>0)
    <table class="table table-striped table-bordered table-hover dt-responsive">
      <thead>
        <tr class="title">
          <td>Nº</td>
          <td>Motivo (Referencia)</td>
          <td>Venta</td>
          <td>Fecha</td>
          <td>Total</td>
          <td>Pagado</td>
          <td>Pendiente</td>
          <td>Ver</td>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item_key => $item)
          <tr>
            <td>{{ $item_key + 1 }}</td>
            <td>{{ $item->name }} ({{ $item->reference }})</td>
            <td>
              @if($item->sale)
                {{ $item->sale->name }}
              @else
                -
              @endif
            </td>
            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $item->amount.' '.$item->currency->name }}</td>
            <td>{{ $item->paid_amount.' '.$item->currency->name }}</td>
            <td>{{ $item->pending_amount.' '.$item->currency->name }}</td>
            <td><a target="_blank" href="{{ url('admin/model/'.$type.'/edit/'.$item->id) }}">Ver</a></td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="title">
          <td colspan="3">Pagadas: {{ round($paid, 2).' '.$currency->name }}</td>
          <td colspan="3">Pendientes: {{ round($pending, 2).' '.$currency->name }}</td>
          <td colspan="3">Total: {{ round($total, 2).' '.$currency->name }}</td>
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