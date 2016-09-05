@extends("layout")
@section("content")
<div class="h1">{{$caption}}</div>

@if($shares->count() == 0 && $type !== "help")
<h2 style="width:80%;margin:0 auto; text-align: center;">
    Уважаемые покупатели! Сейчас действующих акций и распродаж нет, но они обязательно появятся в скором времени
</h2>
@else

<?php 
if($type === "help") $path = "help/";
else {
    $path = "/shares/share/";
}
        ?>
@foreach($shares as $i => $share)
<span class="num">{{ $i +1 }}.</span><a class="share_link" href="{{$path}}{{$share->id}}">{{$share->name}}</a>
<div class="clear"></div>
@endforeach

@endif
@stop


