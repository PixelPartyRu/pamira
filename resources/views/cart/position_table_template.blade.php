
<div class="table {{ \App\User::getLoginUserType() }}">
<div class="product_info_row caption table-row">
        <div class="table-cell"></div>
        <div class="table-cell">Фото</div>
        <div class="table-cell name">Наименование</div>
        <div class="table-cell count">Кол-во, шт</div>
        <div class="table-cell cost_sell">Цена, руб.</div>
        @if(\App\User::getLoginUserType() == "dealer")
        <div class="table-cell">Скидка ,%</div>
        <div class="table-cell price">Сумма без скидки ,руб.</div>
        <div class="table-cell price">Сумма со скидкой ,руб.</div>
        @else
        <div class="table-cell price">Сумма ,руб.</div>
        @endif

        <div class="table-cell delete"></div>

    </div>
<?php $i=1; ?>

@foreach($order->products as $product_info)
<?php if(is_null($product_info)) continue; ?>
<?php if(is_null($product_info->product)) continue; ?>
<?php if($product_info->product_id == 0) continue; ?>
<div class="product_info_row product_info_row_data table-row brick small" data-id="{{$product_info->id}}">
    <div class="table-cell id">{{$i++}}<input class="hidden_id" type="hidden" name="id_order[]" value="{{$product_info->id}}"></div>
    <div class="table-cell photo"><div><img src="/imgresize?file={{public_path()}}/uploads/product/img1/{{$product_info->product->img}}" /></div></div>
    <div class="table-cell name">{{$product_info->product->name}}</div>

    <div class="table-cell edit count">
        <div class="value">{{$product_info->count_product}}</div>
        <input type="number" name="count_product" value="{{$product_info->count_product}}"/>
    </div>
    <div class="table-cell edit_cost edit">
        <div class="value cost_value">{{$product_info->product->getFormatCost() }}</div>
        <input type="text" name="cost_trade" value="{{$product_info->product->getRoundCost() }}"/>
    </div>
    @if(\App\User::getLoginUserType() == "dealer")
    <div class="table-cell edit discount">
        <div class="value">{{$product_info->discount}}</div>
        <input type="text" min="0" max="100" name="discount" value="{{$product_info->discount}}" />
    </div>
    <div class="table-cell price cost_without_discount">{{ $product_info->getFormatPositionSumm() }}</div>
    @endif

    <div class="table-cell price cost_with_discount">
        <span>
        @if(\App\User::getLoginUserType() == "dealer")
        {{ $product_info->getFormatCostWithDiscount() }}
        @else
        {{ $product_info->getFormatPositionSumm() }}
        @endif
        </span>
        <input type="text" name="cost_with_discount" value="{{ (\App\User::getLoginUserType() == "dealer")?$product_info->getFormatCostWithDiscount():$product_info->getFormatPositionSumm() }}_">
    </div>

    <div class="table-cell delete">
        @if($order->status != 1)
        <a href="#">Х</a>
        @endif
    </div>
</div>
@endforeach
    <div class="product_info_row total table-row">
        <div class="table-cell id "></div>
        <div class="table-cell photo"></div>
        <div class="table-cell name">Итого:</div>
        <div class="table-cell count count_total">{{ $order->getProductsCountAll() }}</div>
        <div class="table-cell cost_sell edit_cost"></div>
        @if(\App\User::getLoginUserType() == "dealer")
        <div class="table-cell discount"></div>
        <div class="table-cell price cost_without_discount total_price_without_discount">{{ $order->getFormatProductsSumm() }}</div>
        @endif
        <div class="table-cell price cost_with_discount total_price_with_discount">{{ $order->getFormatSumWithDiscount() }}</div>
        <div class="table-cell delete"></div>
    </div>
</div>