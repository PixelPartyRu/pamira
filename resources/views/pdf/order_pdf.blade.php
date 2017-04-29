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
            table-layout: fixed;
        }
        h1 {
            font-size: 18px
        }
        h2 {
            font-size: 14px
        }
        h3 {
            font-size: 12px
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
            padding:5px 0px 5px 0px;
        }
        .row.head > div{
            height:40px;

        }
        </style>
    </head>
    <body style="font-size:9.5pt">
        <table>
            <tbody>
                <tr>
                    <?php /* <td style="text-align: left"><img class="w300" src="{{ public_path() }}/img/html_586a784f.png"></td>
                    <td style="text-align: right"><img class="w300" src="{{ public_path() }}/img/html_28a4ef9f.jpg"></td>  */ ?>
                    <div style="clear: both;"></div>
                </tr>
            </tbody>
        </table>

        <div class="date">Дата заявки: {{ date('d.m.Y', strtotime($order->created_at))}}г</div>

        <h1 class="center">Заявка №{{ $order->id }}</h1>

        <h2>Организация-заказчик: {{ $dealer->name }}</h2>
        <h2>Адрес заказчика: {{ $dealer->sns }}</h2>

        <div class="div-table" style="">
            <div class="row head">
                <div style="width:20px;">№</div>
                <div style="width:90px;">Артикул</div>
                <div>Наименование</div>
                <div style="width:40px;">Кол-во</div>
                <div style="width:25px;">Ед.</div>
                <div style="width:80px;">Сумма ОПТ</div>
                <div style="width:80px;">Сумма РРЦ</div>
            </div>
            <?php $i=1; ?>
            @foreach($order->products as $product_info)
            <div class="row">
                <div>{{$i++}}</div>        
                <div class="article">{{ implode(" ", str_split($product_info->product->article, 16)) }}</div>        
                <div>{{$product_info->product->name}}</div>        
                <div>{{$product_info->count_product}}</div>        
                <div>шт.</div>        
                <div>{{ number_format(sprintf("%.2f",$product_info->getOptCostWithDiscount()),2,","," ") }}</div>  
                <div>{{ number_format(sprintf("%.2f",$product_info->getCostWithDiscount()),2,","," ") }}</div>
            </div>
            @endforeach
            <div class="row">
                <div></div>  
                <div></div>    
                <div class="bold">Итого: </div>
                <div class="bold">{{ $order->getProductsCountAll() }}</div>
                <div></div>
                <div class="bold">{{ number_format(sprintf("%.2f",$order->getOptSumWithDiscount()),2,","," ") }}</div> 
                <div class="bold">{{ number_format(sprintf("%.2f",$order->getSumWithDiscount()),2,","," ") }}</div>
            </div>



        </div>
        Всего наименований {{ count($order->products) }}, на сумму {{ number_format($order->getOptSumWithDiscount(),2,","," ") }} руб<br />
        
    </body>
</html>