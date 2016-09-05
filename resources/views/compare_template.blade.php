
{{--<div class="cart compare_cart">
    <h1 style="font-size: 16px;">Сравнение товаров</h1>
    <div class="cart_container">
        <div>Количество товаров: <a class="summ">5</a></div>
        <div><a href="#">Перейти к сравнению</a></div>
        
    </div>
</div>--}}

@if(!is_null(\App\Dealer::getLoginDealer()))
<?php $compare_order_list = \App\Compare::getUsersPositions("order")->get_positions(); ?>
<div class="cart compare_order" {{ count($compare_order_list) == 0?"style=display:none":"" }}>
    <h1 style="font-size: 16px;">Сравнение заказов</h1>
    <div class="cart_container">
        <span class="after" ></span>

        @foreach($compare_order_list as $order_c)
        <div class="line_order after" oid_line="{{ $order_c->id }}"><a href="/dealer/completed_order/{{ $order_c->id }}">Заказ №{{ $order_c->id }}</a></div>
        @endforeach
        <div><a class="go_to" href="/compare_dealer/order_compare_list">Перейти к сравнению</a></div>
        <div><a class="clear_compare" href="#">Очистить сравнение</a></div>
    </div>
</div>
@endif