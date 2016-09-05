@extends("layout")
@section("content")
<div class="h1">
    {{ $order->status==0?"Завершение оформления заказа":"Заказ №".$order->id. " от " . $order->getFormatComplitedDate() }}
</div>
{{ Form::open(['url' => '/dealer/cart?step_token'.csrf_token(), 'method' => 'post','id' => 'option_completed_order']) }}
<div class="cart_block">


    <div class="cart_list dragging">
        <div class="edit_button in_no_edit_mode top">
<!--           <button type="button" id="edit" class="float_right">Редактировать</button>-->
            @if($order->status==1 && $order->admin_status==0)
            <a href="/dealer/restore_order_in_cart/{{$order->id}}" id="cart_restore" class="float_right">Отменить заказ и перенести позиции в корзину</a>
            @endif
        </div>
    @include("cart.position_table_template",$order)
        <div class="edit_button in_no_edit_mode bottom completion_button centered">
<!--            Атрибут id содержит тип действия при отправке формы-->
@if($order->status == 1)
<div class="complited_order_links bottom">
    <a href="/dealer/save_client_order_pdf_by_id/{{$order->id}}" class="button button_submit float_left">Сохранить заказ для Клиента</a>
    <a href="/dealer/save_order_pdf_by_id/{{$order->id}}" class="button button_submit ">Сохранить заказ для Поставщика</a>
    <a href="/dealer/caterer_mail_send_by_id/{{$order->id}}" class="caterer_mail_send_button button float_right">Отправить заказ Поставщику</a>
</div>
@endif
@if($order->status == 0)

            {{ Form::submit('Сохранить заказ для Клиента',array("id"=>"save_client_pfd","class"=>'button button_submit float_left')) }}
            {{ Form::submit('Сохранить заказ для Поставщика',array("id"=>"save_caterer_pfd","class"=>'button button_submit ')) }}
            {{ Form::submit('Отправить заказ Поставщику',array("id"=>"caterer_mail_send","class"=>'button float_right')) }}
@endif
        </div>

    </div>
</div>
{{ Form::close() }}




@stop