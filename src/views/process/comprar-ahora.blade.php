@extends('layouts/master')
@include('helpers.meta')

@section('css')
@endsection

@section('header')
<!-- Banner Area Start -->
<div class="banner-area pb-90 pt-160 bg-2">
  <div class="container">
    <div class="row">
      <div class="banner-content text-center text-white">
        <h1>{{ $page->name }}</h1>
        <ul>
          <li><a href="{{ url('') }}">inicio</a> <span class="arrow_carrot-right "></span></li>
          <li>{{ $page->name }}</li>
        </ul> 
      </div>
    </div>
  </div>
</div>
<!-- Banner Area End -->
@endsection

@section('content')
<div class="container">
  @include('store::includes.buy-now')
</div>
@endsection

@section('script')
<script type="text/javascript">
  $('a.delete').click(function (event) {
    $(this).parents('tr').first().remove();
    event.stopPropagation();
    return false;
  });
</script>
@endsection