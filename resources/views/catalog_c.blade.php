@extends("layout")
@section("content")
<div class="dev_page">
<div class="interval"><input type="text" /></div>
<ul class="scripts">
    <li class="script_elem" data-script="catalog/cache_all/">Скрипт кеширования каталога</li>
    <li class="script_elem" data-script="/test/migratePh/">Параметры фильтра из базы товаров</li>
    <li class="script_elem" data-script="/test/setProductHFormat/">Формат текста</li>
     <li class="script_elem" data-script="/test/testduh/">Получение типа</li>
      <li class="script_elem" data-script="/test/getSize/">Настройка ширины</li>
      
       <li class="script_elem" data-script="/test/proverkaBrand/">proverkaBrand</li>
        <li class="script_elem" data-script="/test/proverkaType/">proverkaType</li>
         <li class="script_elem" data-script="/test/proverkaColor/">proverkaColor</li>
    
    
    
</ul>

@foreach($catalog as $cat)
<div class="cat_id">{{ $cat->id }}</div>
@endforeach

<div class="button button_base">Начать</div>
<script>
    $(document).ready(function() {
        var t;
        $(".cat_id").click(function() {
            $(this).addClass("selected");
        });
        
        $(".script_elem").click(function() {
            $(".script_elem").removeClass("selected");
            $(this).addClass("selected");
            //alert( $(this).data("script") );
            
        });
        
        function send() {
            console.log("send");
            var script = $(".script_elem.selected").data("script");

            
            if($(".cat_id.selected:not(.end)").size() == 0) {
               clearInterval(t);
               return 0;

                
            }
            var cat = $(".cat_id.selected:not(.end)").first();
            console.log(script);
            console.log(script+cat.html());
            console.log(cat.html());
            cat.addClass("end");

            $.get(script+cat.html(),function(data){
                console.log("script_send");
                console.log(data);
                
            });

            
            
            
        }
        
        $(".button").click(function() {
            var interval = $(".interval input").val();
            alert(interval);
            
            t = setInterval(send, interval);
            console.log(t);
        });
       // setInterval(send, 20000);

        
        
        
        
        
    });
</script>
</div>
@stop