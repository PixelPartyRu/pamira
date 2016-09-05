@extends("layout")
@section("content")
<div class="h1">Сравнение заказов</div>

<table class="order_compare_table">
    
    <tr class="header">
<!--        <div class="table-cell type"></div>-->
        @foreach($orders as $order)
        <td order_id="{{ $order->id }}">
            <div class="wrap">
            <span class="order_num" order_id="{{ $order->id }}"><a href="/dealer/completed_order/{{ $order->id }}">Заказ №{{ $order->id }}</a></span>
            <span class="cross_del" order_id="{{ $order->id }}"><img src="/img/b_red.png"></span>
            </div>
        </td>
        @endforeach
    </tr>
    @foreach($types as $type)
    <tr class="body type_caption">
        
        <td colspan="3"> {{ $type->value }} </td>
        
    </tr>
    <tr class="body">
<!--        <div class="table-cell type">{{$type->value}}</div>-->
        @foreach($orders as $order)
        <td  order_id="{{ $order->id }}">
            <?php $products = $order->getProductsByType($type->value); ?>
            @foreach($products as $product)
            <a href="/product_catalog/get/{{ !is_null($product->product->catalog)?$product->product->catalog->alias:"_" }}/{{ $product->product->alias}}" class="img" >
<!--                <img src="/imgresize?file={{public_path()}}/uploads/product/img1/{{$product->img}}" />-->
                <img src="http://pamira.tw1.ru/imgresize?file=uploads/product/img1/{{$product->product->img}}" />
                <span class="name">{{$product->product->name}}</span>
                <span class="price">{{ $product->getFormatCostWithDiscount() }} руб.</span>
            </a>
            @endforeach
            
        </td>
        @endforeach
        
    </tr>
    @endforeach
    

    
</table>
<div class="compare_btns"><a class="button" href="/dealer/save_compare_pdf">Сохранить сравнение в PDF</a></div>
<script src="/js/order_compare.js"></script>

@stop