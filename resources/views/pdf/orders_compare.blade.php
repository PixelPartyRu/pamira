<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
        body { 
            font-family: DejaVu Sans;
            font-size:9.5pt
        }
        .h1 {
            font-size: 18px;
            text-align: center;
        }
        .center{
            text-align: center;
        }
        .bold{
            font-weight:bold;
        }

        .order_compare_table {
            width: 100%;
            margin-bottom: 20px;
        }
          .order_compare_table .body {
            text-align: center;
        }
          .order_compare_table .type_value {
            text-align: center;
        }
          .order_compare_table .type_caption td {
            border-bottom: 1px dotted black;
            font-weight: bold;
            font-size: 13px;
            color: #000;
            text-align: center;
            padding-top: 10px;
            padding-bottom: 10px;
        }
          .order_compare_table td {
            width: 33% !important;
            text-align: center;
        }
          .order_compare_table td .wrap {
            display: inline-block;
        }
          .order_compare_table td .wrap .cross_del,
          .order_compare_table td .wrap .order_num {
            float: left;
            display: inline-block;
        }
          .order_compare_table td .wrap .order_num a {
            color: #0747bb;
            font-size: 16px;
            font-weight: bold;
        }
          .order_compare_table td .wrapper {
            padding-top: 10px;
            padding-bottom: 10px;
            display: block;
            width: 100%;
            text-align: center;
        }
        .order_compare_table td .img {
            height: 180px;
        }
          .order_compare_table td .wrapper img {
            width: 100px;
        }
        
          .order_compare_table td .wrapper .name {
            display: block;
            width: 100%;
            font-size: 15px;
            margin: 10px 0;
        }
          .order_compare_table td .wrapper .price {
            display: block;
            width: 100%;
            font-size: 12px;
            color: black;
            margin-bottom: 10px;
        }
          .order_compare_table .header .td {
            text-align: center;
        }
        </style>
    </head>
    <body >
        <div class="h1">Сравнение заказов</div>

        <table class="order_compare_table">
            @foreach($types as $type)
            <tr class="body type_caption">
                <td colspan="3"> {{ $type->value }} </td>
            </tr>
            <tr class="body">
                @foreach($orders as $order)
                <td  order_id="{{ $order->id }}">
                    <?php $products = $order->getProductsByType($type->value); ?>
                    @foreach($products as $product)
                    <div class="wrapper" >
                        <img width="50%" src="{{ public_path() }}/uploads/product/img1/{{$product->product->img}}" />
                        <span class="name">{{$product->product->name}}</span>
                        <span class="price">{{ $product->getFormatCostWithDiscount() }} руб.</span>
                    </div>
                    @endforeach
                </td>
                @endforeach
            </tr>
            @endforeach
        </table>
    </body>
</html>