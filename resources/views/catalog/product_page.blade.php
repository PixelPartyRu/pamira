@extends("layout")
@section("content")
<div class="product_page">
    <div class="breadcrumbs">
        <div class="main_page breadcrumb"><a href="/">Главная</a></div>
        <div class="catalog_path breadcrumb">
            @if(!is_null($product->catalog))
            <a href="/catalog/{{ $product->catalog->alias }}">{{ $product->catalog->name }}</a>
            @endif
        </div>
        <div class="clear"></div>
    </div>
    @include("product_page")

</div>


@stop