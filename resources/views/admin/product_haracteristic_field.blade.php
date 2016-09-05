
<div class="row product_haracteristic_field">
    <div class="col-sm-3">{!!  Form::select($data->name, $data->options, $data->value, $data->attributes) . $data->extra_output !!}</div>
    <div class="col-sm-3">{!!  Form::text( $data->name."_new",$value = null, $attributes = array('placeholder'=>'Значение','data-name' => $data->name,'class' => 'new_value') ) !!} </div>
    @if($data->name == "color_id")
    <div class="col-sm-3">{!!  Form::text( $data->name."_new",$value = null, $attributes = array('placeholder'=>'Код цвета','class' => 'color_code') ) !!}</div>
    @endif
    <div class="col-sm-3"><a class="add_new" href='#'>Добавить</a></div>
</div>    