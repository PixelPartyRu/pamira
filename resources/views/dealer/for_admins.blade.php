@extends("layout")
@section("content")
<div class="margin_list">
<?php /* <div class="create_margin"><a href="/dealer/margin_create">Создать наценку</a></div> */ ?>
    <h2><a href="/dealer/margin_create?margin_type=wholesale">Установка розничной цены<a/></h2>
<div class="margin_table table">
    @foreach($wholesale_margin as $margin)
    <div class="table-row">
        <div class="table-cell">{{ $margin->name }}</div>
        <div class="table-cell">
            <strong class="default_margin" style="{{ $margin->default==1?'':'display:none;' }}">установлена по умолчанию</strong>
            <a href="#" style="{{ $margin->default==0?'':'display:none;' }}" class='set_default' data-id="{{$margin->id}}">Установить по умолчанию</a>
        </div>
        <div class="table-cell"><a href="/dealer/margin_edit/{{ $margin->id }}">ред.</a>,</div>
        <div class="table-cell"><a href="#" class="delete" data-id="{{$margin->id}}" >удалить</a></div>
    </div>
    @endforeach

</div>
    <h2><a href="/dealer/margin_create?margin_type=retail">Установка оптовой цены</a></h2>
<div class="margin_table table">
    @foreach($retail_margin as $margin)
    <div class="table-row">
        <div class="table-cell">{{ $margin->name }}</div>
        <div class="table-cell">
            <strong class="default_margin" style="{{ $margin->default==1?'':'display:none;' }}">установлена по умолчанию</strong>
            <a href="#" style="{{ $margin->default==0?'':'display:none;' }}" class='set_default' data-id="{{$margin->id}}">Установить по умолчанию</a>
        </div>
        <div class="table-cell"><a href="/dealer/margin_edit/{{ $margin->id }}">ред.</a>,</div>
        <div class="table-cell"><a href="#" class="delete" data-id="{{$margin->id}}" >удалить</a></div>
    </div>
    @endforeach
</div>
</div>
<script type="text/javascript" src="/js/min/margin_list.min.js"></script>
@stop