@extends("layout")
@section("content")
<div class="h1">Помощь в выборе</div>

<div class="news_lis dib wh">
    
    @foreach($news as $a)
    <div class="article">
        <div class="article_inner">

        
        <div class="header_article">
            <a href="/help/{{$a->alias}}">{{ $a->name }}</a>
        </div>
            @if($a->img !== "")
            <img src="/uploads/content/{{$a->img}}">
            @endif
       <div class="text"> {!! strip_tags(mb_substr($a->text,0,400)) !!} ...</div>
        <div class="date_and_more">
                         <a href="/help/{{$a->alias}}">Читать далее...</a>
             <div class="date">Дата публикации: {{$a->getFormatDate()}}</div>


         </div>
        </div>   
        
    </div>    
    @endforeach

</div>
@stop