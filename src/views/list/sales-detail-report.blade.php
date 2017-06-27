@extends('master::layouts/admin')

@section('content')
  <h1>Detalle de Ventas</h1>

  @include('store::helpers.report.filter')

  @if(count($items)>0)
    <table class="table table-striped table-bordered table-hover dt-responsive">
      <thead>
        <tr class="title">
          <td>Nº</td>
          <td>Código</td>
          <td>Artículo</td>
          <td>Vendedor</td>
          <td>Socio</td>
          <td>Precio</td>
          <td>Descuento</td>
          <td>Cantidad</td>
          <td>Total</td>
          <td>Fecha</td>
          <td>Por Cobrar</td>
          <td>Abrir</td>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item_key => $item)
          <tr>
            <td>{{ $item['count'] }}</td>
            <td>{{ $item['item']->product->barcode }}</td>
            <td>{{ $item['item']->product->category->name.' - '.$item['item']->product->name }}</td>
            <td>{{ $item['sale']->user->name }}</td>
            <td>{{ $item['item']->product->partner->name }}</td>
            <td>{{ $item['item']->price.' '.$item['item']->currency->name }}</td>
            @if($item['sale']->invoice)
              <td>{{ number_format($item['item']->product->invoice_price - $item['item']->price, 2, '.', '').' '.$item['item']->currency->name }}</td>
            @else
              <td>{{ number_format($item['item']->product->price - $item['item']->price, 2, '.', '').' '.$item['item']->currency->name }}</td>
            @endif
            <td>{{ $item['item']->quantity }}</td>
            <td>{{ $item['total'].' '.$item['item']->currency->name }}</td>
            <td>{{ $item['sale']->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $item['pending'] }}</td>
            <td><a target="_blank" href="{{ url('admin/model/sale/view/'.$item['sale']->id) }}">Abrir</a></td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3">Ventas Cobradas: {{ round($paid, 2).' '.$currency->name }}</td>
          <td colspan="3">Ventas por Cobrar: {{ round($pending, 2).' '.$currency->name }}</td>
          <td colspan="3">Total de Envíos: {{ round($shipping, 2).' '.$currency->name }}</td>
          <td colspan="3">Total de Ventas: {{ round($total, 2).' '.$currency->name }}</td>
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