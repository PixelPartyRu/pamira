@if(!is_null(\App\User::getLoginCustomerOrUser()))
<div class="cart">
    <h1>Корзина</h1>
    <div class="cart_container">
        <div>В Вашей корзине:</div> 
        <div><a class="count">{{ !is_null(\App\User::getLoginCustomerOrUser()->getCurentOrder())?\App\User::getLoginCustomerOrUser()->getCurentOrder()->getProductsCount():0 }}</a> товар</div>
        <div>на сумму 
            <a class="summ">{!! !is_null(\App\User::getLoginCustomerOrUser()->getCurentOrder())?number_format(  \App\User::getLoginCustomerOrUser()->getCurentOrder()->getProductsSumm()  ,  0  ,  ','  ,  ' '  ):0 !!}</a> руб.</div>
        <div><a href="/{{ \App\User::getLoginUserType() }}/cart">Перейти в корзину</a></div>
    </div>   
</div>    

@else
<div class="cart no_vis">
    <h1>Корзина</h1>
    <div class="cart_container">
        <div>В Вашей корзине:</div> 
        <div><a class="count">2</a> товар</div>
        <div>на сумму 
            <a class="summ">36 140</a> руб.</div>
        <div><a href="/customer/cart">Перейти в корзину</a></div>
    </div>   
</div>
@endif



