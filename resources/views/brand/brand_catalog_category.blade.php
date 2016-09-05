@extends("layout")
@section("content")
@include("brand.breadcrumbs")

@if($products->count() == 0)
<p class="empty_message">
    Раздел находится в процессе заполнения. Информацию по товарам, пожалуйста, уточняйте у оператора номер_телефона
</p>    
    
@endif

<div class="product_list">
    @foreach($products as $product)
    @include('catalog.product_template')
    @endforeach
    <div class="clear"></div>
    @include('pagination.default', ['paginator' => $products])
</div>    
@stop