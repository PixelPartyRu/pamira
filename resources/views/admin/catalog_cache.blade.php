@extends('panelViews::mainTemplate')
@section('page-wrapper')
<style>
    ul li{
        list-style-type: none;
        display:inline-block;
        width:100%;
        padding:5px;
            
    }
    ul li input{
        float:left;
    }
    ul li div{
        float:left;
        display:inline-block;
        margin-top:3px;
        margin-left:5px;
    }
    
        ul li.complete div:before{
        float:right;
        display:inline-block;
        content:" - Готово";
        color:red;
        margin-left:5px;
       
    }
            ul li.wait div:before{
        float:right;
        display:inline-block;
        content:" Ждите...";
        color:red;
        margin-left:5px;
       
    }
</style>

<ul>
    <li><a href="#" class="all">Отметить все</a></li>
    @foreach($catalog as $cat)
    <li class="" num="{{ $cat->id }}"><input type="checkbox" class="check"><div style="margin-right:5px">{{ $cat->name }}</div></li>
    @endforeach
</ul>

<div class="btn button btn-info">Кешировать</div>

<script src="/js/catalog_cache.js"></script>
@stop