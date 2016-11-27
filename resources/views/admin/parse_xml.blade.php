@extends('panelViews::mainTemplate')
@section('page-wrapper')
<style>
    #parts_line{
        display:inline-block;
        width:100%;
        height:20px;
    }
    #parts_line .line_elem{
        display:inline-block;
        height:20px;
    }
    #parts_line .line_elem.load{
        background-color: blue;
    }
    .console .console_line{
        width:50%;
        float:left;
    }
        .console .console_info{
        width:100%;
    }
</style>
<div class="container">
    <div class="h1">Обновление товаров из XML</div>
  @if(!isset($parts))
    <div class="row">
        {{ Form::open( array("enctype" => "multipart/form-data") ) }}
        <div class="form-group">
            <label for="exampleInputFile">Выберите файл</label>
            {{ Form::file("file") }}
            <p class="help-block">
            </p>
        </div>
        <div class="form-group">
            {{ Form::submit() }}
        </div>
        {{ Form::close() }}
    </div>
  @endif

    @if(isset($parts))
    <p>Подождите</p>
    <div id ="stopwatch"></div>

    <div class="row parse_info">
        <div id="parts_line" class="parts" count="{{ $parts }}">

        </div>
    </div>
    <div class="button btn btn-warning start">Начать парсинг</div>
    <p>Всего товаров в файле {{ $count_product }}</p>
    <p>После обновления необходимо пройти процедуру кеширования каталога.</p>

    <p>Консоль</p>
    <div class="console" style="width:100%; height:600px; border-top:1px solid black;"></div>

    @endif

</div>

<script src="/js/parse_xml.js"></script>

@stop