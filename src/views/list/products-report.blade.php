@extends('master::layouts/admin')

@section('css')
  @include('master::scripts.lightbox-css')
  @endsection

@section('content')
  <h1>Detalle de Inventario</h1>

  @include('store::helpers.report.filter')

  @if(count($items)>0)
    <table class="table table-striped table-bordered table-hover dt-responsive">
      <thead>
        <tr class="title">
          <td>Nº</td>
          <td>Código</td>
          <td>Artículo</td>
          <td>Socio</td>
          <td>Costo de Compra</td>
          <td>Cantidad</td>
          <td>Total de Compra</td>
          <td>Fecha</td>
          <td><i class="fa fa-refresh"></i></td>
          <td><i class="fa fa-close"></i></td>
        </tr>
      </thead>
      <tbody>
        <?php $last_place = 0; ?>
        @foreach($items as $item_key => $item)
          @if($place=='all')
            @if($last_place!=$item->place_id)
              <?php $last_place = $item->place_id; ?>
              <tr class="title"><td colspan="10">Sucursal: {{ $item->place->name }}</td></tr>
            @endif
          @endif
          <tr>
            <td>{{ $item_key+1 }}</td>
            <td>{{ $item->parent->barcode }}</td>
            <td>{{ $item->parent->category->name.' - '.$item->parent->name }}</td>
            <td>{{ $item->parent->partner->name }}</td>
            <td>{{ $item->parent->cost.' '.$item->parent->currency->name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format(round($item->parent->cost * $item->quantity, 2), 2, '.', '').' '.$item->parent->currency->name }}</td>
            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
            <td><a class="lightbox" href="{{ url('admin/transfer-product-stock/'.$item->id.'?lightbox[width]=800&lightbox[height]=500') }}" title="Transferir a otra Sucursal"><i class="fa fa-refresh"></i></a></td>
            <td><a class="lightbox" href="{{ url('admin/remove-product-stock/'.$item->id.'?lightbox[width]=800&lightbox[height]=500') }}" title="Quitar de Stock"><i class="fa fa-close"></i></a></td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="title">
          <td colspan="9">Total de Valor de Inventario: {{ round($total, 2).' '.$currency->name }}</td>
        </tr>
      </tfoot>
    </table>
  @else
    <p>No hay registros de ingresos o egresos para las fechas seleccionadas.</p>
  @endif
@endsection
@section('script')
  @include('master::scripts.lightbox-js')
  @include('store::helpers.report.datepicker')
@endsection