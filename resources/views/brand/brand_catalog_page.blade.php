@extends("layout")
@section("content")
@include("brand.breadcrumbs")
@foreach($categories as $cat)

@endforeach
<div class="catalog_brand">
    @foreach($categories as $i => $cat)
    <div class="brand_catalog_elem">
    <div class="img">
        <a href="/brand/{{ $brand->alias }}/{{ $cur_catalog->alias }}/{{ App\Jobs\Helper::translit($cat->value) }}">
        <img src="/imgresize?file={{public_path()}}{{ $images[$i] }}" />
        </a>
    </div>
    <a href="/brand/{{ $brand->alias }}/{{ $cur_catalog->alias }}/{{ App\Jobs\Helper::translit($cat->value) }}">{{ $cat->value }}</a>
    </div>
    @endforeach
</div>
@stop