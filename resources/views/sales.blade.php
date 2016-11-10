@extends("layout")

@section("content")
<div class="h1">Распродажи</div>

@if(isset($products))
<!--Если это текущая страница, то ссылка делается не кликабельной-->
<div class="breadcrumbs">
    <div class="main_page catalog_path breadcrumb"><a href="/sales">Распродажи</a></div>

    <div class="catalog_path breadcrumb">

        <a href="">{{ $catalog_products->name }}</a>
    </div>

</div>

<div class="product_list">
    @each('catalog.product_template', $products , 'product')
<div class="clear"></div>
</div>
@endif

@if(isset($catalogs))
<div class="catalog_brand">
    @foreach($catalogs as $i => $cat)
    <div class="brand_catalog_elem">
    <div class="img">
        <a href="/sales/{{ $cat->alias }}">
        <img src="/imgresize?file={{public_path()}}/uploads/product/img1/{{ $cat->getCatalogImgForSales() }}" />
        </a>
    </div>
    <a href="/sales/{{ $cat->alias }}">{{ $cat->name }}</a>
    </div>
    @endforeach
</div>
@endif


@stop