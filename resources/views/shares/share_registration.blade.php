@extends("layout")
@section("content")
<?php 
if($type === "help") $path = "help/help_registration";
else {
    $path = '/shares/share_registration';
}
?>
<div class="h1">{{ $caption }}</div>


<div class="share_auth_form reg_form">
    <div class="form_caption">Регистрация участника</div>

    {{ Form::open(array('url' => $path, 'method' => 'post','class' => '')) }}
    
    {{ Form::hidden($type) }}
    @if(!is_null($share_id))
    {{ Form::hidden('share_id',$share_id) }}
    @endif

    <div class="form_row">
    <div class="cell">
    <label>ФИО</label>
    </div>
    
    <div class="cell">    
    <div class="input">{{ Form::text('name') }}</div>
    </div>
        
    </div>
    
    <div class="form_row">
    <div class="error">
        {{ isset($form_errors) && isset($form_errors["name"])?"Введите ФИО":"" }}
    </div>
    </div>
    
    <div class="form_row">
    <div class="cell">   
    <label>Салон</label>
    </div>
    
    <div class="cell">       
    <div class="input">{{ Form::text('salon') }}</div>
    </div>
        
    </div>
    
    <div class="form_row">
    <div class="error">
        {{ isset($form_errors) && isset($form_errors["salon"])?"Введите салон":"" }}
    </div>
    </div>    
    
    <div class="form_row">
    <div class="cell">   
    <label>Адрес</label>
    </div>
    
    <div class="cell">      
    <div class="input">{{ Form::text('adress') }}</div>
    </div>
    </div>
    
    <div class="form_row">
    <div class="error">
        {{ isset($form_errors) && isset($form_errors["adress"])?"Введите адрес":"" }}
    </div>
    </div>
    
    <div class="form_row">
    <div class="cell">   
    <label>Пароль</label>
    </div>
    
    <div class="cell">    
    <div class="input">{{ Form::password('password') }}</div>
    </div>
    </div>
    
    <div class="form_row">
    <div class="error">
        {{ isset($form_errors) && isset($form_errors["password"])?"Введите пароль":"" }}
    </div>
    </div>
    
    <div class="form_row captcha_row">
        <div class="cell"> 
            <label>Код</label>
        </div>
        <div class="cell">
            <img class="captcha" src="/shares/captcha" />
            <div class="input">{{ Form::text('captcha') }}</div>
        </div>
        
    </div>
    
    
    <div class="form_row">
    <div class="error">
        {{ isset($form_errors) && isset($form_errors["captcha"])?"Введите код с картинки":"" }}
    </div>
    </div>
    
    <div class="form_row">
        <div class="cell">
            <label>Регион</label>
        </div>

        <div class="cell">    
            <div class="input">{{ Form::select('region_id',$regions_p) }}</div>
        </div>
    </div>
    <div class="form_row"></div>
    
    <div class="form_row">
    <div class="cell">
    <label>Email</label>
    </div>
    
    <div class="cell">    
    <div class="input">{{ Form::text('email') }}</div>
    </div>
    </div>
    
    <div class="form_row">
    
    <div class="error">
        {{ isset($form_errors) && isset($form_errors["email"])?"Введите email":"" }}
    </div>
    </div>
    
    <div class="form_row submit_row">
    {{ Form::submit('Зарегистрироваться') }}
    </div>



    {{ Form::close()}}
</div>





@stop


