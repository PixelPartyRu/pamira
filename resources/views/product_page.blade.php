<div class="product_header">{{ $product->name }}</div>
    <div class="gallery">
        <div class="base_image {{ $product->sales_leader == 1?"sales_leader":"" }}">
            @if($product->sales_leader == 1)
            <div class="sticker_wrapper">
            <div class="sticker">&nbsp;</div>
            </div>
            @endif
           <a href="{{ $product->img !==''?'/uploads/product/img1/'.$product->img:'/img/no_image.png' }}" rel="slb_off" class="img_link">
               <img src="{{ $product->img !==''?'/uploads/product/img1/'.$product->img:'/img/no_image.png' }}" rel='slb_off' />
           </a>
        </div>
        <div class="image_slider">
            @foreach($product_img as $k => $img)
            @if($img !== "")
            <div class="img img{{ ($k + 1) }} {{ ($k==0)?'active':'' }}">
                <div class="img_wrapper">
                    <a href="{{ '/uploads/product/img'.($k + 1).'/'.$img }}" class="lightbox">
                        <img src="{{ '/uploads/product/img'.($k + 1).'/'.$img }}" />
                    </a>
                </div>
            </div>
            @endif
            @endforeach

            <div class="clear"></div>
        </div>

    </div>
    <div class="product_base_info">
        <div class="inner_wrapper">
        <div class="brand_link">
            <img src="/uploads/brands/{{ strtolower($product->brand->img) }}" />
        </div>
        @if($product->viewcost)
        <div class="cost_trade"><span> Цена: {{ $product->getFormatCost() }} руб.</span></div>

            {{-- ОПТ и Наценка --}}

            @if( \App\Dealer::is_login() )
            <div class="cost_trade gb-cost-wholesale" id="gb-cost-wholesale"><span> Опт: {{ number_format($product->getCostWithMargin(true),0,',',' ') }} руб.</span></div>
            <div class="cost_trade gb-cost-mark-up" id="gb-cost-mark-up"><span> Наценка: {{ $markup }}%</span></div>
            @endif

        <div class="buy_button" data-id="{{ $product->id }}"><img src="/img/button_buy1.gif"></div>
        @else
        <div class="cost_trade"><span>Уточните цену у менеджера</span></div>
        @endif
        </div>
        @if(!is_null(\App\User::getLoginUserType()))
        <div class="compare" data-id="4">

            {{--
            <span class="compare_img"><img src="/img/compare.png"></span>
            <span><a href="" id="compare_add" pid="{{$product->id}}">Добавить в сравнение</a></span>
            <div class="clear"></div>
            --}}

        </div>
        @endif

    </div>
    <div class="product_description">
        <div class="articul">Артикул: {{ $product->article }}</div>
        <div class="haracteristic">
            <span>Описание товара/Технические характеристики:</span>
            <div class="text">

                {!! $product->haracteristic !!}

            </div>


        </div>
        <?php //var_dump($product->getHaracteristicValue("color")); ?>
        @if(!is_null($product->getHaracteristicValue("color")))
        <div class="color">Цвет: {{ $product->getHaracteristicValue("color") }} </div>
        @endif
        <div class="country">Страна изготовления: {{ $product->country }} </div>
    </div>
    <div class="clearfix"></div>
    @if(!empty($product_analogs))
    <h3 class="title product_header gb-other-colors">Другие расцветки:</h3>
    <div class="product_analogs product_list">
        @each('catalog.product_template', $product_analogs , 'product')
    </div>
    @endif

 <link rel="stylesheet" href="/css/simplelightbox.min.css">

<script type="text/javascript" src="/js/modules/simple-lightbox.min.js"></script>
<script type="text/javascript" src="/js/min/product.min.js"></script>
<script type="text/javascript" src="/js/compare.js"></script>