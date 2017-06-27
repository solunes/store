@extends('master::layouts/admin')

@section('content')
  <h1>Contabilidad: Balance General</h1>

  @include('store::helpers.report.filter')

  @if(count($items)>0)
    @foreach($items as $type => $item)
      <h3>{{ trans('master::admin.'.$type) }}</h3>
      <table class="table table-striped table-bordered table-hover dt-responsive">
        <thead>
          <tr class="title">
            <td>Concepto</td>
            <td>Monto</td>
          </tr>
        </thead>
        <tbody>
          <?php $total = 0; ?>
          @foreach($item as $concept => $subitem)
            <tr class="title">
              <td colspan="2">{{ $concept }}</td>
            </tr>
            <?php $subtotal = 0; ?>
            @foreach($subitem as $subconcept => $subaccount)
              <tr>
                <td>{{ $subconcept }}</td>
                <td>{{ $subaccount.' '.$currency->name }}</td>
              </tr>
              <?php $subtotal += $subaccount; ?>
            @endforeach
              <tr>
                <td>TOTAL</td>
                <td>{{ $subtotal.' '.$currency->name }}</td>
              </tr>
              <?php $total += $subtotal; ?>
          @endforeach
          <tr class="title">
            <td>TOTAL DE {{ trans('master::admin.'.$type) }}</td>
            <td>{{ $total.' '.$currency->name }}</td>
          </tr>
        </tbody>
      </table>
    @endforeach
  @else
    <p>No hay registros de ingresos o egresos para las fechas seleccionadas.</p>
  @endif
@endsection
@section('script')
  @include('store::helpers.report.datepicker')
@endsection