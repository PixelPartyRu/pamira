<div data-id = "{{ $product->id }}" class="product_box {!! $product->sales_leader == 1?'with_sticker':'' !!}">
    @if($product->sales_leader == 1 )
    <div class="sticker">&nbsp;</div>
    @endif
    <a href="/product_catalog/get/{{ !is_null($product->catalog)?$product->catalog->alias:"_" }}/{{ $product->alias}}" class="img" >
        <img src="/imgresize?file={{public_path()}}/uploads/product/img1/{{$product->img}}" />
    </a>
    @if($product->viewcost)
    <div class="price_info">{{  $product->getFormatCost() }} руб.</div>
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