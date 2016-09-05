<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
        body { 
            font-family: DejaVu Sans;
        }
        .w300{
            width: 200px;
            margin-bottom: 10px;
        }
        table{
            width: 100%;
        }
        .center{
            text-align: center;
        }
        .bold{
                    font-weight:bold;
            }
        .div-table{
               display:table;
               width:100%;
               font-size:12px;
               border-collapse:separate;
               text-align:center;
               vertical-align:middle;
        }
        .row{
            display:table-row;	
        }
        .row > div{
               display:table-cell;
               border:1px solid black;
               vertical-align:middle;
               padding:3px 0px 3px 0px;
        }
        .row > div.desc{
            padding:0px !important;
            font-size:5.5pt !important;
            text-align:justify;
            vertical-align:top !important;
        }

        .row.head > div{
               height:40px;

        }
        .nowrap {
            white-space: nowrap;
        }
        .with_img img {
            max-width: 100px;
            max-height: 100px;
        }
        .img-cell {
            text-align: center;
            vertical-align: middle;
        }
        .price {
            font-size: 11px;
        }
        </style>
    </head>
    <body style="font-size:9.5pt">
        <div class="date">Дата заявки: {{ date('d.m.Y', strtotime($order->created_at))}}г</div>
        <h1 class="center">Заявка №{{ $order->id }}</h1>
        <h2 class="center small">на поставку кухонных моек, смесителей, техники, вытяжек</h2>
        <h2>Клиент: {{ $order->sns }}</h2>
        <div class="div-table" style="">
            <div class="row head">
                <div style="width:20px;">№</div>
                <div style="width:90px;">Артикул</div>
                <div>Наименование</div>
                <div style="width:40px;">Кол-во</div>
                <div style="width:25px;">Ед.</div>
                <div style="width:75px;">Цена</div>
                <div style="width:50px;">Скидка<br/>%</div>
                <div style="width:75px;">Сумма без скидки</div>
                <div style="width:75px;">Сумма со скидкой</div>
            </div>
            <? $i=1; ?>
            @foreach($order->products as $product_info)
            <div class="row">
                <div>{{$i++}}</div>        
                <div class="article">{{ implode(" ", str_split($product_info->product->article, 16)) }}</div>        
                <div>{{$product_info->product->name}}</div>        
                <div>{{$product_info->count_product}}</div>        
                <div>шт.</div>
                <div>{{ number_format( round( $product_info->product->getCostWithMargin(), -1 ) ,2,","," ") }}</div>        
                <div>&nbsp;{{$product_info->discount}}</div>        
		<div class="price">{{ $product_info->getFormatPositionSumm() }}</div>            
                <div class="price">{{ number_format($product_info->getCostWithDiscount(),2,","," ") }}</div>            
            </div>
            @endforeach
            <div class="row"><div></div>
                <div></div>    
                <div class="bold">Итого: </div>
                <div class="bold">{{ $order->getProductsCountAll() }}</div>
                <div></div>
                <div></div>
                <div></div>
                <div class="bold nowrap price">{{ number_format($order->getProductsSumm(),2,","," ") }}</div>
                <div class="bold nowrap price">{{ number_format($order->getSumWithDiscount(),2,","," ") }}</div>
            </div>
        </div>
        
        Всего наименований {{ count($order->products) }}, на сумму {{ number_format($order->getSumWithDiscount(),0,"."," ") }},00 руб <br>
        <?php /*Контактное лицо подавшее  <br>
        заявку и контактный телефон: ______________________________________  <br> */ ?>

        <!--</div>-->

        <div style="page-break-after: always;"><span style="display:none;">&nbsp;</span></div>
        
        <div class='div-table'>

            <div class='row' style='font-size:10px;' >
                <div style='width:115px;' >Картинка</div>
                <div style='width:80px;' >Артикул</div>
                <div style='width:90px;' >Наименование</div>
                <div >Описание</div>
                <div style='width:80px;'>Сумма со скидкой</div>
            </div>
            {{--*/ $k = 0; /*--}}
            @foreach($order->products as $product_info)
            {{--*/ $haract = str_replace ( '&amp;', ' ', strip_tags($product_info->product->haracteristic) ); /*--}}
            @if($k == 8)
                {{--*/ $k = 0; /*--}}
        </div>    
        <div style="page-break-after: always;"><span style="display:none;">&nbsp;</span></div>
        <div class='div-table'>
            <div class='row' style='font-size:10px;' >
                <div style='width:115px;' >Картинка</div>
                <div style='width:80px;' >Артикул</div>
                <div style='width:90px;' >Наименование</div>
                <div >Описание</div>
                <div style='width:80px;'>Сумма со скидкой</div>
            </div>
            @endif
            {{--*/ $k++; /*--}}
            
            <div class='row with_img' style='border:1px solid red;font-size:10px;'>
		<div class="img-cell"><img src="{{ public_path() }}/uploads/product/img1/{{ $product_info->product->img }}"></div>
		<div class="article">{{ implode(" ", str_split($product_info->product->article, 16)) }}</div>
                <div>{{$product_info->product->name}}</div>
                <div class="desc" ><div style="padding:1px 10px">{!! (strlen($haract)>700) ? substr($haract, 0,700) . '...': $haract !!}</div></div>
                <div class="price">{{ number_format($product_info->getCostWithDiscount(),0,"."," ") }}.00</div>
            </div>
            @endforeach
            <div class='row'>
                <div></div>
                <div></div>
                <div></div>
                <div style='font-weight:bold;' >Итого: </div>
                <div class="price" style='font-weight:bold;'>{{ number_format($order->getSumWithDiscount(),0,"."," ") }}.00</div>
            </div>
        </div>
    </body>
</html>