<!--            верхняя чать сайта, поиск и меню-->
<div class="row menu-row"> 

    <div class="logo">
    <a class="main_href" href="/">
            <img src="/img/logo_menu.png" />
        </a>
        </div>
    <div class="tel_wrap">
    <div class="tel">
        <a class="big">302-00-22</a>
        <a class="min"><span class="city">Ростов-на-Дону</span> +7 (863)</a>


    </div>
    
    <div class="tel last">
        <a class="big">253-30-20</a>
        <a class="min"><span class="city">Воронеж</span> +7 (473)</a>   
    </div>
        </div>

    <div class="clear"></div>

</div> 

<div class="row menu-row menu_link">
    <div class="menu">
        <ul>



            <li>
                <a href="/shares" {{ $cur_path === "shares"?"class=active":"" }} >
                    <span>Акции</span>

                </a>
            </li>
            <li>
                <a href="/sales">
                    <span>Распродажи</span>
                </a>
            </li>
            <li>
                <a href="/content/article_page/contacts" {{ $cur_path === "content/article_page/contacts"?"class=active":"" }} >
                   <span> Контакты </span>
                </a>
            </li>
            <li>
                <a href="/content/article_page/about" {{ $cur_path === "content/article_page/about"?"class=active":"" }} >
                   <span> О компании </span>

                </a>
            </li>

            @if( !is_null(\App\User::getLoginUserType()) )
            <li>
                <a href="/news" {{ $cur_path === "content/article"?"class=active":"" }} >
                   <span>  Новости  </span>
                </a>
            </li>
            @endif

            <li><a href="/help" {{ $cur_path === "help"?"class=active":"" }}>
                    <span> Помощь в выборе </span>
                </a></li>
            <li class="last"><a href="#">  <span>Виртуальный тур </span></a></li>





            <!--                        <li><a href="/content/article_page/delivery">Доставка</a></li>
                                    <li><a href="/content/article_page/installation">Установка</a></li>-->




        </ul>
    </div>
</div>