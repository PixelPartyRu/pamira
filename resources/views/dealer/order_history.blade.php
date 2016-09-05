@extends("layout")
@section("content")
<div class="h1">История заказов</div>
<div class="order_history_list table wh">
    <div class="table-row header">
        <div class="table-cell img"></div>
        <div class="table-cell">Номер</div>
        <div class="table-cell">Клиент</div>
        <div class="table-cell">Дата</div>
        <div class="table-cell">Сумма,руб</div>
        <div class="table-cell">Статус</div>
        <div class="table-cell"></div>
    </div>
    @foreach($orders as $order)
    <div class="table-row orderrow">
        <div class="table-cell img" style="">
            <a href="#" class="compare_order_link" oid="{{$order->id}}">
            <img src="/img/green_cross.png">
            </a>
        </div>
        <div class="table-cell">
            <a class="id" href="/dealer/completed_order/{{$order->id}}">{{ $order->id }}</a>
        </div>
        <div class="table-cell">{{ $order->sns}}</div>
        <div class="table-cell">{{ $order->getFormatComplitedDate() }}</div>
        <div class="table-cell">{{ $order->getFormatSumWithDiscount() }} </div>
        <div class="table-cell">{{ $order->getAdminStatus() }} </div>
        <div class="table-cell"><a class="red remove_order" href="/dealer/remove_order/{{$order->id}}">x</a></div>
    </div>    
    @endforeach
    
</div>    
<script type="text/javascript" src="/js/order_compare.js"></script>
    
@stop