<!--        Окно авторизации дилера-->
        <div id="diller_login_container" class="popup_container">
            <div class="popupWindowShadow" id="cartPopupWindow_layer"><div class="clear">&nbsp;</div></div>
            <div id="dealer_login_form" class="popup_box">
                <div class="popupClose"></div> 
                @if( is_null(\App\Dealer::getLoginDealer()) )
                <h2>Авторизация дилера</h2>
                <div class="error">Неверный пароль</div>
                <label>Пароль:</label><input id="dealer_password" type="password" name="password" />
                <div class="clear"></div>
                <div class="button">Войти</div>
                @else
                <h2>Вы авторизированы как дилер</h2>
                @endif
                
            </div>
        </div>
<!--        Сообщения-->
        <div class="popup_container" id="message_window_box">
            <div class="popupWindowShadow" id="cartPopupWindow_layer"><div class="clear">&nbsp;</div></div>
            <div id="message_window" class="popup_box">
                <div class="before_add"><img src="/img/478.gif" /></div>
                <div class="after_add" style="display:none">
                <div class="message">Товар добавлен в корзину</div>
                <div class="button ok">ok</div>
                </div>
                
            </div>
        </div>
        
<!--        регистрация юзера при покупке-->
        <div class="popup_container" id="customer_form_box">
            <div class="popupWindowShadow" id="cartPopupWindow_layer"><div class="clear">&nbsp;</div></div>
            <div class="popup_box customer_form_box">
            <div class="popupClose"></div> 
            <h2>Заполните данные для формирования заказа</h2>
            
            <label>ФИО:</label><input id="dealer_password" type="text" name="sns" />
            <div class="error">&nbsp;</div>
            <label>Телефон:</label><input id="dealer_password" type="text" name="phone" />
            <div class="error">&nbsp;</div>
            <label>Электронная почта:</label><input id="dealer_password" type="text" name="email" />
            <div class="error">&nbsp;</div>
            <label>Регион:</label>
            <select name="region_id">
                @foreach($regions as $region)
                <option value="{{ $region->id}}">{{ $region->name }}</option>
                @endforeach
            </select>
            <div class="clear"></div>
            <div class="button">Сохранить</div>
            </div>
        </div>

        <div class="popup_container" id="no_name_box">
            <div class="popupWindowShadow" id="cartPopupWindow_layer"><div class="clear">&nbsp;</div></div>
            <div id="message_window" class="popup_box">
                <div class="after_add">
                <div class="message">Заполните поле ФИО</div>
                <div class="button ok">ok</div>
                </div>
            </div>
        </div>
