@extends("layout")
@section("content")
<h2>Результат поиска по запросу: <span class="bold"> {{ $search_word }}</span></h2>
<div class="product_list">
    @each('catalog.product_template', $products , 'product')
<div class="clear"></div>
</div>
@stop