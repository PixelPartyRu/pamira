@extends("layout")
@section("content")
<div class="h1">Новости</div>

<div class="news_lis dib wh">
    
    @foreach($news as $a)
    <div class="article">
        <div class="article_inner">

        
        <div class="header_article">
            <a href="/news/{{$a->alias}}">{{ $a->name }}</a>
        </div>
            <img src="/uploads/content/{{$a->img}}">
       <div class="text"> {!! strip_tags(mb_substr($a->text,0,400)) !!} ...</div>
        <div class="date_and_more">
                         <a href="/news/{{$a->alias}}">Читать далее...</a>
             <div class="date">Дата публикации: {{$a->getFormatDate()}}</div>


         </div>
        </div>   
        
    </div>    
    @endforeach

</div>
@stop