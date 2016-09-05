@extends("layout")
@section("content")
<div class="h1">Оформление заказа</div>
{{ Form::open(array('url' => '/dealer/cart', 'method' => 'post')) }}	
<input type="hidden" name="step_token" value="{{ csrf_token() }}">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="cart_block">
    <div class="client_name_field">
        <label>Введите ФИО клиента:</label>
        <div class="input {{ !empty($errors->first('name'))?"no_valid":"" }}"> 
            {{ Form::text("name") }} 
        </div>
        <div class="clear"></div>
    </div>
    <div class="cart_list">
    @include("cart.position_table_template",$order)
        <div class="edit_button in_no_edit_mode bottom">
            <input type="hidden" name="step" value="3" />
            {{ Form::submit('Подтвердить заказ',array("class"=>'button float_right')) }}
            <a href="/dealer/cart?step=1" class="button float_left">Назад</a>
        </div>

    </div>
</div>
{{ Form::close() }}
@if(!empty($errors->first('name')))
<script>

$(function(){
    $("#no_name_box").show();
});

</script>
@endif
@stop