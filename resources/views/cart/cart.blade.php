@extends("layout")
@section("content")

<div class="cart_block">
<div class="h1">Корзина</div>

@if($order->products->count() == 0)
<div> Корзина пуста </div>
@else
<div class="cart_list" data-id="{{$order->id}}">
    @if(\App\User::getLoginUserType() == "dealer")
    <div class="edit_button in_edit_mode top discount_edit" >
        <button type="button" id="edit_cost" class="float:left">Установить общую скидку на заказ</button>
        <input type="text" min="0" max="100" id="all_discount" />
        <button type="button" id="change_costs" class="float_right change_costs">Установить цены вручную</button>
    </div>
    <div class="edit_button in_no_edit_mode top">
        <button type="button" id="edit" class="float_right">Редактировать</button>
    </div>
    @endif
    
@include("cart.position_table_template",$order)

@if(\App\User::getLoginUserType() == "dealer")
<div class="edit_button in_no_edit_mode bottom">
    <form action="/dealer/cart" method="post">
        <input type="hidden" name="step_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="step" value="2">
        <input type="submit" id="formalize_order" class="button float_right" value="Оформить заказ" />
    </form>
</div>
<div class="edit_button in_edit_mode bottom" >
    <button type="button" id="save_form" class="fl">Сохранить изменения</button>
    <div class="clear"></div>
    <button type="button" id="withoutSaveButton" class="fl">Выйти без сохранения</button>
</div>
@endif
@if(\App\User::getLoginUserType() == "customer")
<div class="edit_button bottom">

<a data-user="{{ is_null($order->customer->sns)?0:1 }}" class="button  save_customer_pdf" href="/customer/save_order_pdf/{{ $order->id }}">Сохранить заказ</a>   
<button type="button" id="send_meneger_order" class="fr">Отправить заказ менеджеру</button>   

</div>
@endif

</div>
@endif




</div>
<script src="/js/modules/inputmask.js"></script>
<script src="/js/modules/inputmask.numeric.extensions.js"></script>
<script>

$(document).ready(function() {
    
    var im = new Inputmask("9{1,3}");

    
    im.mask("[name='discount']");
    im.mask("#all_discount");

});			
			

</script>


@stop