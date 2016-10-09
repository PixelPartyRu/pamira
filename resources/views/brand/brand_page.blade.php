@extends("layout")
@section("content")
@include("brand.breadcrumbs")
<div class="big_left_title">Техника {{ $brand->title }}</div>
<div class="brand_img">
<img src="/uploads/brands/{{ strtolower($brand->img) }}">
</div>
<div class="slogan">
    {!! $brand->getSlogan() !!}
</div>
<div class="catalog_brand">
    @foreach($catalogs as $brand_catalog)
    <div class="brand_catalog_elem">
    <div class="img">
        <a href="/brand/{{ $brand->alias }}/{{ $brand_catalog->alias }}">
        <img src="/imgresize?file={{public_path()}}/uploads/product/img1/{{ $brand_catalog->getRandomImgByBrand($brand->id) }}" />
        </a>
    </div>
    <a href="/brand/{{ $brand->alias }}/{{ $brand_catalog->alias }}">{{ $brand_catalog->name }} {{ $brand->title }}</a>
    </div>
    @endforeach
</div>

<div class="seo_text">
    {!! $brand->getSEOtext() !!}
</div>
@stop