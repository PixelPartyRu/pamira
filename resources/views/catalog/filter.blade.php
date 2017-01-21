
<div class="filter">
    <div class="filter_tooltip">
        <div class="close"></div>
        <div class="clear"></div>
        <div class="product_info"><span>Товаров : <a class="count">2</a></span><span><a class="show_link" href="">Показать</a></span></div>
        <div class="ajax_message">Идет подсчёт...</div>
    </div>
    <div class="beauty_filter_line"></div>

    <div class="filter_brands">
            <label class="caption">Бренд</label>
        <div class="filter_parts">

            @foreach($filter_brand as $f_brand)
            <div class="brand_filter_elem brand{{ $f_brand->id }}" >
            <input type="checkbox" name="brand[]" value="{{ $f_brand->id }}"/>
            <label class="filter_label">{{ $f_brand->title }}</label>
            </div>
            @endforeach
        </div>

    </div>
    <div class="product_haracteristic_filter">
        <input type="hidden" name="catalog" value="{{ $catalog_ob->id }}">

        <?php //dd($filters); ?>
        @foreach($filters as $filter)
        <div class="product_haracteristic_filter_elem">
            <label>{{ $filter->label }}</label>
        <div class="select {{ $filter->name }}" name="{{ $filter->name }}">
<!--            <div class="select_line"></div>-->
            <div class="select_checkboxes">
                @if($filter->name == "color")
                <div class="clear">
                <?php $i = 1; ?>
                @foreach($filter->values as $key=>$color)
                <?php if( $color["value"] == "" ) continue; ?>
                <div class="select_checkbox color  {{ $filter->name }}{{$color['id']}}">
                    <input type="checkbox" name="{{ $filter->name }}[]" value="{{$color['id']}}" class="gb-color" />
                    <div class="color_sq" style="background-image: url(/uploads/color/{{ transliterate(str_replace(" ","%20",$color['value']))  }}.jpg)">&nbsp;</div>
                    <label class="filter_label">{!!str_ireplace(' ',"<br />",$color['value'])!!}</label>
                </div>
                <?php $i++ ?>
                @if($i > 5)
                </div><div class="clear">
                <?php $i = 1; ?>
                @endif
                @endforeach
                </div>
                @else
                <?php $i = 1; ?>
                <div class="clear">
                @foreach($filter->values as $value=>$label)
                @if($label !== "")
                <div class="select_checkbox  {{ $filter->name }}{{$value}}">
                    <input type="checkbox" name="{{ $filter->name }}[]" value="{{$value}}"/>
                    <label class="filter_label">{{$label}}</label>
                </div>
                <?php $i++ ?>
                    @if($i > 5)
                    </div><div class="clear">
                    <?php $i = 1; ?>
                    @endif
                @endif
                @endforeach
                </div>
                @endif
            </div>
        </div>
        </div>
        @endforeach
        <div class="product_haracteristic_filter_elem">
            <!-- Цена -->
            <div class="product_haracteristic_filter_elem_price">
                <label>Цена</label>
                <div class="select" name="sort">
                    <!--                <div class="select_line"><div class="arrow">&nbsp;</div></div>-->
                    <div class="select_checkboxes">

                        <div class="formCost">
                            <label for="minCost">от</label> <input type="text" id="minCost" value="-1">
                            <label for="maxCost">до</label> <input type="text" id="maxCost" value="-1">
                        </div>
                        <div class="sliderCont">
                            <div id="slider" data-max="{{ round($filter_max_price) }}" data-min="{{ round($filter_min_price) }}"></div>
                        </div>

                    </div>

                </div>
            </div>

            <!--        Сотировка-->
            <div class="product_haracteristic_filter_elem_sort">
                <label>Сортировка</label>
                <div class="select" name="sort">
                    <!--        <div class="select_line"><div class="arrow">&nbsp;</div></div>-->
                    <div class="select_checkboxes">

                        <div class="select_checkbox">
                            <input type="radio" name="sort" value="name" />
                            По наименованию
                        </div>
                        <div class="select_checkbox">
                            <input type="radio" name="sort" value="cost_trade" checked="checked"/>
                            По цене
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>
    <div class="clear"></div>
    <div class="line">&nbsp;</div>

    <input type="hidden" id="is_products_filtered" name="is_products_filtered" value="0" />
    <div class="filter_buttons">
    <div class="filter-products button">Выбрать товары</div>
    <div class="reset button">Сбросить фильтр</div>
    </div>
</div>