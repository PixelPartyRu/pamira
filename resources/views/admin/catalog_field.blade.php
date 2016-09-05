@if($data->status == "create")
<div class="row catalog_field">
    <div class='col-sm-3'>{!!  Form::select($data->name, $data->first_level['options'],0,array("class"=>"form-control")) !!}</div>
    <div class='col-sm-3'>{!!  Form::select($data->name, array(), 0, array("class"=>"hidden second_level_catalog")) !!}</div>
    <div class='col-sm-3'>{!!  Form::select($data->name, array(), 0, array("class"=>"hidden third_level_catalog")) !!}</div>
</div>
@endif

@if($data->status == "modify")
<div class="row catalog_field">
    <div class='col-sm-3'>{!!  Form::select($data->name, $data->first_level['options'],0,array("class"=>"form-control")) !!}</div>
    
    <div class='col-sm-3'>
        {!!  Form::select($data->name, $data->second_level['options'], $data->second_level['value'], $data->second_level['attr']) !!}

    </div>
    
    <div class='col-sm-3'>{!!  Form::select($data->name, $data->third_level['options'], $data->third_level['value'], $data->third_level['attr'] ) !!}</div>
</div>

@endif

