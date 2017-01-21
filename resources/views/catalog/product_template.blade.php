<div data-id = "{{ $product->id }}" class="product_box {!! $product->sales_leader == 1?'with_sticker':'' !!}" style="position: relative">
    <!-- <div class="sticker_wrapper" style="border: 2px solid red; min-height: 70px"> -->
        @if($product->sales_leader == 1 )
        <div class="sticker">&nbsp;</div>
        @endif

        @if($product->sticker_promo == 1 )
        <div class="sticker-promo">&nbsp;</div>
        @elseif($product->sticker_action == 1 )
        <div class="sticker-action">&nbsp;</div>
        @endif
    <!-- </div> -->

    <a href="/product_catalog/get/{{ !is_null($product->catalog)?$product->catalog->alias:"_" }}/{{ $product->alias}}" class="img" >
        <img src="/imgresize?file={{public_path()}}/uploads/product/img1/{{$product->img}}" />
    </a>
    @if($product->viewcost)
    <div class="price_info">{{  $product->getFormatCost() }} руб.</div>

        <div class="cost_trade">
            @if( $product->product_in_stock() )

                <span class="delivery-time">Есть в наличии</span>

            @elseif ( $product->product_delivery_time_of_five_to_ten_days() )

                <span class="delivery-time">Срок поставки 5-10 дней</span>

            @elseif ( $product->specify_the_terms_of_delivery_of_goods() )

                <span class="delivery-time product-missing">Уточните сроки поставки</span>

            @else

                <span class="delivery-time product-missing">Уточните сроки поставки</span>

            @endif
    </div>

    <div class='buy'><div class='buy_button' data-id = '{{ $product->id }}'><img src='/img/button_buy1.gif' /></div></div>
    @else
    <div class="price_info">Уточните цену у менеджера</div>
    <div class='buy'></div>
    @endif
    @if(isset($brand_alias))
    <a href="{{ $product->getPathAliasForBrand() }}" title="" class="product_name">
        {{$product->name}}
    </a>
    @else

    <a href="/product_catalog/get/{{ !is_null($product->catalog)?$product->catalog->alias:"_" }}/{{ $product->alias}}" title="" class="product_name">
        {{$product->name}}
    </a>
        @endif


</div>