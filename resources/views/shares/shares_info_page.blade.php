@extends("layout")
@section("content")
<?php 

if($type === "help") $path = "help/";
else {
    $path = "/shares/share/";
}
?>
<?php 
if($type === "help") $reg_path = "/help/help_registration";
else {
    $reg_path = '/shares/share_registration';
}

?>


<div class="h1">{{$share->name}}</div>
@if($share->auth == 1 && is_null($user))

<div class="share_auth_form">
@if(isset($is_login) && !$is_login)
<div class="error">Неверный email/пароль</div>
@endif
<div class="form_caption">Авторизация</div>

{{ Form::open(array('url' => $path.$share->id, 'method' => 'post','class' => '')) }}

{{ Form::hidden($type) }}

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
    {{ isset($form_errors) && isset($form_errors["captcha"])?"Введите код c картинки":"" }}
</div>
</div>

<div class="form_row submit_row">
{{ Form::submit('Войти') }}
</div>

{{ Form::close() }}

</div>
<div class="row center">
<a class="share_reg_link" href="{{$reg_path}}?share_id={{ $share->id }}">Зарегистрироваться</a>
</div>


@endif

@if($share->auth == 1 && !is_null($user))
<div class="share_description"><p>{!! $share->description !!}</p></div>
<table class="share_table">
    <tr>
        <td>Участник</td>
         <td>Салон</td>
        <td>Баллы</td>
    </tr>
    @foreach($share_p as  $sh)
    <tr>
        <td>{{$sh->name}}</td>
        <td>{{$sh->salon}}</td>
        <td>{{ is_null($sh->getPoints($share->id))?0:$sh->getPoints($share->id)}}</td>    
    </tr>
    @endforeach
    
    @endif
    
</table>

@if($share->auth == 0)
<div class="share_description"><p>{!! $share->description !!}</p></div>
@endif




@stop


