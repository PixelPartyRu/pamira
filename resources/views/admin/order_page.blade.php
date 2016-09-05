@extends('panelViews::mainTemplate')
@section('page-wrapper')
<style>
    .status_links{
        display:none;
    }
    .status_links.show{
        display:block;
    }
</style>

{!! $filter !!}

<div class="order_data">
<div class="row">
Заказ N{{$order->id}} от {{ $order->getFormatComplitedDate() }}
</div>
<div class="row">
Ф.И.О. заказчика: 
@if($order->getCustomerType() == "user")
{{ $order->customer->sns }}
@else
{{ $order->customer->name }}
@endif
</div>
<div class="row">
Данные для связи с заказчиком: {{ $order->customer->phone }}

</div>
    
@if($order->admin_status == 0)
<div class="row status_links status0 {{ $order->admin_status==0?"show":""  }}">
Статус заказа: ожидание (<a status="1" href="/panel/Orders/executed?order_id={{$order->id}}">выполнен</a>/ <a status="2"  href="/panel/Orders/nulled?order_id={{$order->id}}">аннулировать</a>).
</div>
@endif


<div class="row status_links status1 {{ $order->admin_status==1?"show":""  }}">
Статус заказа: выполнен
</div>

<div class="row status_links status2 {{ $order->admin_status==2?"show":""  }}">
Статус заказа: аннулирован
</div>

    
</div>


{!! $grid !!}
<div class="order_data">
<div class="row"><span class="col-lg-3 col-lg-offset-9 right">Итого: {{ $order->getFormatSumWithDiscount() }},00 р</span></div>
</div>

<script>

$(document).ready(function() {

    $(".status_links a").click(function(event) {
        
        event.preventDefault();
        var that = $(this);
        $.get(that.attr("href"),function() {
            
            var status = that.attr("status");
            $(".status_links").removeClass("show");
            $(".status"+status).addClass("show");
            
        });
        
    });
    
});

</script>
@stop   
