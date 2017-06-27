@extends('master::layouts/admin')

@section('content')
  <h1>Dashboard</h1><br><br>
  <div id="dashboard-mesh" class="row">
    @foreach($items as $item_key => $item)
      <a href="{{ $item['url'] }}">
        <div class="col-sm-4 center">
          <img src="{{ asset('assets/img/dashboard/'.$item_key.'.png') }}" />
          <h4>{{ $item['title'] }}</h4>
        </div>
      </a>
    @endforeach
  </div>
@endsection