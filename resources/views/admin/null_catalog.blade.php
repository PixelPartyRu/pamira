@extends('panelViews::mainTemplate')
@section('page-wrapper')
<style>
    table{
        text-align: center;
    }
    table tr td{
        padding:5px;
    }
    tr.end .id{
        color:blue;
        
    }
</style>
<div id="set" class="btn button btn-info">Определить каталоги</div>

<table>
    <tr><td>id</td><td>Имя</td><td>Каталог, определенный системой</td><td colspan="2">Установка каталога вручную</td></tr>
@foreach($products as $p)
<tr class="cat_id" pid="{{$p->id}}">
    <td class="id">{{ $p->id }}</td>
    <td>{{ $p->name }}</td>
    <td class="catalog_name"></td>
    <td>{!!  Form::select("p_".$p->id, $catalog, 0,array("pid" => $p->id )) !!}</td>
    <td class="button_td"><div class="btn btn-info">Установить</div></td>
</tr>

@endforeach
</table>

<script type="text/javascript" src="/js/null_catalog.js"></script>


@stop