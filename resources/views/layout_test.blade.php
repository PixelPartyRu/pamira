<!DOCTYPE html>
<html class="{{ $user_agent }} {{ $is_main_page == 1?"main_page_html":"" }}" >
    <head>
        <title>Встраиваемая техника для кухни, купить встраиваемую технику Franke в Ростове-на-Дону - «Памира»</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/css/style.css">
        <link rel="stylesheet" href="/css/jqueryslidemenu.css">

        @foreach($global_scripts as $global_script)
        {!! Html::script($global_script); !!}
        @endforeach
        @if( isset($scripts) )
        @foreach($scripts as $script)
        {!! Html::script($script); !!}
        @endforeach
        @endif



        <link rel="icon" href="/favicon.ico" type="image/x-icon" />
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<!--        <link rel="stylesheet" href="/public/css/qunit-1.23.0.css">-->

    </head>

    <div id="qunit"></div>
    <div id="qunit-fixture"></div>
<!--    <script src="/public/js/qunit-1.23.0.js"></script>-->  
  <script src="/public/js/test.js"></script>
    <body class="{{ $is_main_page == 1?"main_page":"" }} {{ \App\User::getLoginUserType() }}">

        @include("popups")
        <div class="basic_content">
            <div class="inner_content">
                @yield("content")
            </div>
        </div>  
<!--                            <script src="/public/js/sinon.js"></script>-->
    </body>
</html>