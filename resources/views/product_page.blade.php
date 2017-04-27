<div class="product_header">{{ $product->name }}</div>
    <div class="gallery">
        <div class="base_image {{ $product->sales_leader == 1?"sales_leader":"" }}"  style="position: relative;">
            <div class="sticker_wrapper">
                @if($product->sales_leader == 1)
                <div class="sticker">&nbsp;</div>
                @endif

                @if($product->sticker_promo == 1 )
                <div class="sticker-promo">&nbsp;</div>
                @elseif($product->sticker_action == 1 )
                <div class="sticker-action">&nbsp;</div>
                @endif
            </div>

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
            <?php /*dd($product);*/ ?>
            <img src="/uploads/brands/{{ strtolower($product->brand->img) }}" />
        </div>
        @if($product->viewcost)

            @if($product->is_sales_for_current_product() )

                @unless( $product->getFormatCostOld() == $product->getFormatCost() )
                    <span class="cost_old"><span><span></span>{{ $product->getFormatCostOld() }} р.</span></span>
                @endunless

            @endif

        <div class="cost_trade"><span> Цена: {{ $product->getFormatCost() }} руб.</span></div>

            {{-- ОПТ и Наценка --}}

            @if( \App\Dealer::is_login() )
                @if($product->is_sales_for_current_product() )
                    @unless( number_format($product->getCostWithMargin(true, false),0,',',' ') == number_format($product->getCostWithMargin(true),0,',',' ') )
                        <span class=" cost_old" id="gb-cost-wholesale-old"><span><span></span>{{ number_format($product->getCostWithMargin(true, false),0,',',' ') }} р.</span></span>
                    @endunless
                @endif

                <div class="cost_trade gb-cost-wholesale" id="gb-cost-wholesale"><span> Опт: {{ number_format($product->getCostWithMargin(true),0,',',' ') }} руб.</span></div>
                <div class="cost_trade gb-cost-mark-up" id="gb-cost-mark-up"><span> Наценка: {{ $markup }}%</span></div>
            @endif

        <div class="buy_button" data-id="{{ $product->id }}"><img src="/img/button_buy1.gif"></div>

        {{-- ОСТАТКИ на складе --}}
        <div class="cost_trade">
                @if( $product->product_in_stock() )

                    <span class="delivery-time" style="font-size: 1.25em; font-weight: normal">Есть в наличии</span>

                @elseif ( $product->product_delivery_time_of_five_to_ten_days() )

                    <span class="delivery-time" style="font-size: 1.25em; font-weight: normal">Срок поставки 5-10 дней</span>

                @elseif ( $product->specify_the_terms_of_delivery_of_goods() )

                    <span class="delivery-time product-missing" style="font-size: 1.25em; background: linear-gradient(to bottom, #A5A4A4, #757575); font-weight: normal">Уточните сроки поставки</span>

                @else

                    <span class="delivery-time product-missing" style="font-size: 1.25em; background: linear-gradient(to bottom, #A5A4A4, #757575); font-weight: normal">Уточните сроки поставки</span>

                @endif
        </div>

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

    @if( \App\Dealer::is_login() )
        @if(false !== $ya_market_product)
            <table class="similar-prices">
                <thead>
                    @if(count($ya_market_product->models) > 1)
                    <tr>
                        <th colspan="3">ВНИМАНИЕ: Яндекс маркет возвратил несколько товаров на наш запрос</th>
                    </tr>
                    @endif
                    <tr>
                        <th>Магазин</th>
                        <th>Цена</th>
                        <th>Доступность</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ya_market_product->models as $prod)
                        <?php $offers = $prod->offers; usort($offers, function($a, $b) { return $b->price - $a->price; }) ?>

                        <tr>
                            <th colspan="3">{{ $prod->name }}</th>
                        </tr>
                        @if(count($offers))
                            @foreach($offers as $offer)
                                <tr>
                                    <td>{{ $offer->shopName }}</td>
                                    <td>{{ $offer->price }} руб.</td>
                                    <td>{{ $offer->inStock ? 'В наличии' : 'Нет в наличии' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <th colspan="3">Предложений нет</th>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @else
            <h1 class="product_header" style="text-align: center">Предложения не найдены на яндекс маркете</h1>
        @endif
    @endif

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