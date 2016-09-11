@extends("layout")
@section("content")
{{ Form::open(array('url' => '/dealer/margin_create', 'method' => 'post','class' => 'margin_form')) }}

    <div class="form_line"><p class="mess_field">Значком * отмечены обязательные поля</p></div>
    <div class="form_line two">
    <label>Наименование наценки*:</label>
    <div class="input {{ !empty($errors->getBags())?"no_valid":"" }}">
        {{ Form::text('name') }}
    </div>
    </div>

    <div class="form_line two">
    <label>Установить цену*: </label>
    <div class="input">
    {{ Form::select('type', array('wholesale' => 'Наценка к розничной цене', 'retail' => 'Наценка к оптовой цене'), $margin_type) }}
    </div>
    </div>
    <div class="form_line one">
        <div class="input">{{ Form::checkbox('default') }}</div>
        <label>-  установить эту наценку по умолчанию</label>
    </div>
    <div class="form_line three">
       <label>Установить единую наценку для всех:</label>
       <div class="field">{{ Form::text('margin_for_all') }}</div>
       <div class="button_field">{{ Form::button('Установить',["class" => "set_margin_for_all"]) }}</div>
    </div>

    <div class="brand_table table">
        @foreach($brands as $brand)
        <div class="table-row">
            <div class="table-cell">{{ $brand->title }}</div>
            <div class="table-cell brand_input">
                {{ Form::hidden('brand_id[]',$brand->id) }}
                {{ Form::text( 'brand_margin[]', $value = null, $attributes = array("class" => "margin_brand_value") )  }}
            </div>
        </div>
        @endforeach

    </div>
    {{ Form::submit('Сохранить') }}



{{ Form::close()}}
<script type="text/javascript" src="/js/modules/inputmask.js"></script>
<script type="text/javascript" src="/js/min/margin_form.min.js"></script>

@stop