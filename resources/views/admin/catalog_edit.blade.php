@extends('panelViews::mainTemplate')
@section('page-wrapper')


@if ($helper_message)
	<div>&nbsp;</div>
	<div class="alert alert-info">
		<h3 class="help-title">{{ trans('rapyd::rapyd.help') }}</h3>
		{{ $helper_message }}
	</div>
    @endif

    <p>
        {!! $edit !!}
    </p>
    
    <div class="row">
        <input type="hidden" id="catalog" value="{{$catalog}}"/>
        <h2>Доступные характеристики в каталоге</h2>
        @foreach($ah as $h) 
        <h4>{{ $h->label }}</h4>

        @foreach($h->values as $k => $value)
        @if($h->name !== "color" )
        <span hid="{{$k}}" class="info col-lg-4"><p>{{ $value }}</p></span>
        <span class="col-lg-4"><p>Товаров: <span class="product"></span></p></span>
        <span class="col-lg-4"><p>В кеше: <span class="cache"></span></p></span>
        
        @else
        <span hid="{{$k}}" class="info col-lg-4"><p>{{ $value["value"] }}</p></span>
        <span class="col-lg-4"><p>Товаров: <span class="product"></span></p></span>
        <span class="col-lg-4"><p>В кеше: <span class="cache"></span></p></span>
        
        @endif
        @endforeach

        <hr>
        @endforeach
   <div class="btn" id="start">start</div>
</div>
    <script src="/js/admin_catalog.js"></script>
@stop
