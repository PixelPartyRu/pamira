@extends("layout")
@section("content")
<div class="article_page">
<div class="h1">{{ $article->name }}</div> 
<div class="rubric">
    @foreach($rubrics as $rubric)
    <a class="tag" href="/content/article/{{$rubric}}"><span>{{$rubric}}</span></a>
    @endforeach
</div>
@if($article->type == "article")
<div class="social">
    <div class="social_des">Рассказать о статье: </div>
<script type="text/javascript">(function() {
  if (window.pluso)if (typeof window.pluso.start == "function") return;
  if (window.ifpluso==undefined) { window.ifpluso = 1;
    var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
    s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
    s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
    var h=d[g]('body')[0];
    h.appendChild(s);
  }})();</script>
<div class="pluso" data-background="#ebebeb" data-options="medium,square,line,horizontal,nocounter,theme=03" data-services="vkontakte,odnoklassniki,facebook,twitter,moimir"></div>
</div>
@endif

<div class="article_text">
    @if($article->img !== "")
    <img src="/uploads/content/{{ $article->img }}">
    @endif
    {!! $article->text !!}
</div> 
@if($article->type == "article")
<div class="date">Дата публикации: {{$article->getFormatDate()}}</div>
@endif

</div>
@stop
