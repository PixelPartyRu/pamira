@extends("layout")
@section("content")
<div class="h1">{{ $catalog_ob->name }}</div>
@include("catalog.filter")
<div class="product_list">
@each('catalog.product_template', $product_list , 'product')
<div class="clear"></div>
@include('pagination.default', ['paginator' => $product_list])
</div>
<script type="text/javascript" src="/js/modules/underscore-min.js"></script>
<script type="text/javascript" src="/js/modules/jquery.json-2.3.js"></script>
<script type="text/javascript" src="/js/modules/jquery.ui-slider.js"></script>
<script type="text/javascript" src="/js/filter.js"></script>
<script id="product_template" type="text/template">
         


<div data-id="<%- id %>" class="product_box ">
        <a href="<%- alias %>" class="img">
        <img src="/uploads/product/img1/<%- img %>">
    </a>
    <div class="price_info"><%- cost_trade %> руб.</div>
    <div class="price_info nocost">Уточните цену у менеджера</div>
    <div class="buy"><div class="buy_button" data-id="<%- id %>"><img src="/img/button_buy1.gif"></div></div>
    <a href="<%- alias %>" title="" class="product_name"><%- name %></a>
</div>

</script>

@stop
