<!DOCTYPE html>
<html class="{{ $user_agent }} {{ $is_main_page == 1?"main_page_html":"" }}" >
  <head>
    <title>Встраиваемая техника для кухни, купить встраиваемую технику Franke в Ростове-на-Дону - «Памира»</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
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




  </head>

  <body class="{{ $is_main_page == 1?"main_page":"" }} {{ \App\User::getLoginUserType() }}">

  @include("popups")






 <!--  <div class="container">
  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4">bootstarap</div>
    <div class="col-xs-12 col-sm-6 col-md-4">bootstarap</div>
    <div class="col-xs-12 col-sm-6 col-md-4">bootstarap</div>
  </div>
  </div> -->



  <div class="search-container menu-row ">
    <div class="logo">
      <a class="main_href" href="/">
        <img src="/img/logo_menu2.png" />
      </a>
    </div>
    <div class="center_text">Техника для кухни</div>

    @if(is_null(\App\Dealer::getLoginDealer()))
    <div class="partner_login">
      <a href="" id="dealer_auth">Вход для партнеров</a>
    </div>
    @endif
    <div class="clear"></div>





  <div class="phone-wrapper" style="float:right">
    <div class="phone-group">
      <div class="city-name">
        <span class="city">Ростов-на-Дону: </span>
        <span class="department">розничный отдел</span>
      </div>
      <div class="phone-number">
        <span class="prefix">+7 (863) </span>
        <span class="number">302-03-04</span>
      </div>
      <div class="phone-number">
        <span class="prefix">+7 (919) </span>
        <span class="number">888-6-777</span>
      </div>
    </div>
    <div class="phone-group">
      <div class="city-name">
        <span class="city">Ростов-на-Дону: </span>
        <span class="department">оптовый отдел</span>
      </div>
      <div class="phone-number">
        <span class="prefix">+7 (863) </span>
        <span class="number">302-00-22</span>
      </div>
    </div>
    <div class="phone-group">
      <div class="city-name">
        <span class="city">Воронеж: </span>
        <span class="department-phone-number">
          <span class="prefix">+7 (473) </span>
          <span class="number">253-30-20</span>
        </span>
      </div>
    </div>
  </div>

    <!-- <div class="tel_wrap" style="border: 1px solid blue">
      <div class="tel">
        <a class="big">302-00-22</a>
        <a class="min"><span class="city">Ростов-на-Дону</span> +7 (863)</a>
      </div>
      <div class="tel">
        <a class="big">888-6-777</a>
        <a class="min"><span class="city"></span> +7 (919)</a>
      </div>

      <div class="tel last">
        <a class="big">253-30-20</a>
        <a class="min"><span class="city">Воронеж</span> +7 (473)</a>
      </div>
    </div> -->





    @if(!is_null(\App\Dealer::getLoginDealer()))
    <div class="dealer_menu">
      <div class="hello">Меню дилера: Hello, {{ \App\Dealer::getLoginDealer()->name}}</div>

      {{-- Кнопки для отображения и скрытия Оптовой цены/Наценки --}}
      {{-- <div class="gb-wrapper-for-buttons">
        <span class="current-button" id="gb-button-client">Работа&nbsp;с&nbsp;клиентом</span>
        <span class="" id="gb-button-provider">Работа&nbsp;с&nbsp;поставщиком</span>
      </div> --}}

      <ul>
        <li>

          <ul class="buttons_for_wholesale_prices_and_margins">
            <li>
             <span class="current-button" id="gb-button-client">Работа&nbsp;с&nbsp;клиентом</span>
            </li>
            <li>
              <span class="" id="gb-button-provider">Работа&nbsp;с&nbsp;поставщиком</span>
            </li>
          </ul>

        </li>

        <li>
          <a href="/dealer/order_history" {{ $cur_path === "dealer/order_history"?"class=active":"" }} >
            Заказы
          </a>
        </li>

        <li>
          <a href="/dealer/cart" {{ $cur_path === "dealer/cart"?"class=active":"" }} >
            Корзина
          </a>
        </li>

        <li>
          <a href="/dealer/margin_list" {{ $cur_path === "dealer/margin_list"?"class=active":"" }} >
            Администратор
          </a>
        </li>

        <li>
          <a id="gb-exit" href="/dealer/logout">
            Выход
          </a>
        </li>
      </ul>
    </div>

    @endif
    <div class="clear"></div>


  </div>










<div class="container main">
<div class="row menu-row menu_link">
    <div class="search">
      <div id="search_field" class="min">
        <form action="/search" method="GET">
          <input type="text" name="search" placeholder="Поиск">
          <input type="image" src="/img/search.gif" id="searchSubmit" class="searchsubmitbutton">
        </form>
      </div>
    </div>
    <div class="menu">
      <ul>

        <li>
          <a href="/shares" {{ $cur_path === "shares"?"class=active":"" }} >
            <span>Акции</span>

          </a>
        </li>
        <li>
          <a href="/sales" {{ $cur_path === "sales"?"class=active":"" }} >
            <span class="iridescent_button">Распродажи</span>
          </a>
        </li>
        <li>
          <a href="/content/article_page/contacts" {{ $cur_path === "content/article_page/contacts"?"class=active":"" }} >
             <span>Контакты</span>
          </a>
        </li>
        <li>
          <a href="/content/article_page/about" {{ $cur_path === "content/article_page/about"?"class=active":"" }} >
             <span>О компании</span>

          </a>
        </li>

        @if( !is_null(\App\User::getLoginUserType()) )
        <li>
          <a href="/news" {{ $cur_path === "news"?"class=active":"" }} >
             <span>Новости</span>
          </a>
        </li>
        @endif

        <li><a href="/help" {{ $cur_path === "help"?"class=active":"" }}>
            <span>Помощь&nbsp;в&nbsp;выборе</span>
          </a></li>
        <li class="last"><a href="#">  <span>Виртуальный&nbsp;тур</span></a></li>

      </ul>
    </div>
  </div>



<!--            <div class="row">
        <div class="top_scallop">&nbsp;</div>
      </div> -->


      <!-- Слайдер, реклама и телефоны -->
       @if($is_main_page == 1)
       @include("slider")

       @if($share->count() > 0)
<!--           <div class="share_block">
         <div class="baner_block">
           <img src="/img/promotions.png">
         </div>
         <div class="shares_info">
           @foreach($share as $sp)
           <div class="top_share">
             <img src="/uploads/shares/{{$sp->img}}" />
           </div>

           @endforeach
         </div>



       </div>  -->

       @endif
       @endif




      <!-- Основная часть с контентом, коталогом и брендами -->
      <div class="basic_site_part" id="site_content">

        <div class="catalog">
          <h1>Каталог</h1>
          <div id="myslidemenu" class="jqueryslidemenu">
            <ul>
              @foreach($catalog as $catalog_parts)
              <li>
                <center><img src="/img/menu/{{ $catalog_parts->img }}" alt=""  /></center>
                <a href="/catalog/{{ $catalog_parts->alias }}" title="{{ $catalog_parts->name }}">{{ $catalog_parts->name }}</a>
              </li>
              @endforeach


            </ul>
            <div class="clear"></div>

          </div>
        </div>
        <div class="basic_content">
          <div class="inner_content">
          @yield("content")
          </div>
        </div>
        @include("cart_template")
        @include("compare_template")

        <div class="brands">
          <h1>Бренды</h1>
          <div class="brand_menu">
            @foreach($brands_right as $brand)
            <div class="brand">
              <a href="/brand/{{$brand->alias}}/" title=""><img src="/uploads/brands/{{ $brand->img }}" alt="{{$brand->title}}"></a>
            </div>
            @endforeach
          </div>
          <div class="clear"></div>
        </div>
        <div class="clear"></div>
      </div>

      <!-- Футер -->
      @include("footer")


    </div>

  </body>
</html>