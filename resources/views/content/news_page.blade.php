@extends("layout")
@section("content")
<div class="h1">Статьи</div>
<div class="rubric">
        @foreach($all_rubric_list as $rubric)
        
    <a class="tag" href="/content/article/{{$rubric}}"><span>{{$rubric}}</span></a>
    @endforeach
</div>
@if( isset($rubric_name) )
<div class="rubric_title">Рубрика : {{$rubric_name}}</div>
@endif
<div class="news_lis dib wh">
    
    @foreach($article as $a)
    <div class="article">
        <div class="article_inner">

        
        <div class="header_article">
            <a href="/content/article_page/{{$a->alias}}">{{ $a->name }}</a>
        </div>
            <img src="/uploads/content/{{$a->img}}">
       <div class="text"> {!! strip_tags(mb_substr($a->text,0,400)) !!} ...</div>
        <div class="date_and_more">
                         <a href="/content/article_page/{{$a->alias}}">Читать далее...</a>
             <div class="date">Дата публикации: {{$a->getFormatDate()}}</div>


         </div>
        </div>   
        
    </div>    
    @endforeach

</div>
@stop