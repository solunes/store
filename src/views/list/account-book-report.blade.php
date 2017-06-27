@extends('master::layouts/admin')

@section('content')
  <h1>Libro Mayor</h1>

  @include('store::helpers.report.filter')

  @if(count($items)>0)
    @foreach($items as $item)
      <h3>{{ $item['name'] }}</h3>
      <table class="table table-striped table-bordered table-hover dt-responsive">
        <thead>
          <tr class="title">
            <td>Nº</td>
            <td>Fecha</td>
            <td>Asiento</td>
            <td>Concepto</td>
            <td>Debe</td>
            <td>Haber</td>
          </tr>
        </thead>
        <tbody>
          <?php $total = 0; ?>
          @foreach($item['items'] as $key => $subitem)
              <td>{{ $key+1 }}</td>
              <td>{{ $subitem->created_at }}</td>
              <td><a href="{{ url('admin/account-book-detail?transaction_code='.$subitem->transaction_code) }}" target="_blank">{{ $subitem->transaction_code }}</a></td>
              <td>{{ $subitem->name }}</td>
              <td>@if($subitem->debit) {{ $subitem->debit.' Bs.' }} @endif</td>
              <td>@if($subitem->credit) {{ $subitem->credit.' Bs.' }} @endif</td>
            </tr>
            <?php $total = $total + $subitem->debit - $subitem->credit; ?>
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
          </tr>
        </tfoot>
      </table>
    @endforeach
  @else
    <p>No hay registros de ingresos o egresos para las fechas seleccionadas.</p>
  @endif
@endsection
@section('script')
  @include('store::helpers.report.datepicker')
@endsection