<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<div class="button">111111</div>
<ul>
    @foreach($products as $product)
    <li class="cat_id" pid="{{$product->id}}">{{$product->id}}  {{$product->name}}<span class="catalog"></span></li>
    @endforeach
</ul>


<script>
$(document).ready(function () {


    function send() {

        var cat = $(".cat_id:not(.end)").first();

        cat.addClass("end");
        cat.addClass("wait");

        $.get("/test/set_cat/" + cat.attr("pid"), function (data) {
            console.log("script_send");
            console.log(data);

            cat.append(" " + data);


        });




    }

    $(".button").click(function () {


        send();
    });





    $(document).bind("ajaxError", function (error) {
        console.log("error");
        console.log(error);
    }).bind("ajaxSend", function () {
        console.log("send");

    }).bind("ajaxSuccess", function () {

        if (!$("#parts_line").hasClass("end")) {
            if ($(".cat_id:not(.end)").size() > 0) {
                console.log("next_send");
                $(".cat_id.end").last().removeClass("wait");
                $(".cat_id.end").last().addClass("complete");

                send();
            } else {
                $(".cat_id.end").last().removeClass("wait");
                $(".cat_id.end").last().addClass("complete");
            }
        } else {

        }


    });



});
</script>    