<style>
    table{
        border:1px solid black;
        text-align: center;
        vertical-align:middle;
    } 
</style>    
Всего товаров: {{ count($old) }}
<table border="1">
    <tr style="font-weight:bold">
        <td>Имя</td>

        <td>Каталог</td>
        <td>Подкаталог1</td>
        <td>Подкаталог2</td>
        <td>Подкаталог3</td>
        @foreach($attrs as $atr) 
        <td>{{ $atr }}</td>
        @endforeach
    </tr>
    @foreach($old as $p)
    <tr>
        <td>{{ $p->product->name }}</td>
        <td>{{ $p->id_cat }}</td>
        <td>{{ $p->id_podcat1 }}</td>
        <td>{{ $p->id_podcat2 }}</td>
        <td>{{ $p->id_podcat3 }}</td>
        @foreach($attrs as $atr) 
        <td>{{ isset($p->ph[$atr])?$p->ph[$atr]:"Нет" }}</td>
        @endforeach
        
    </tr>
    @endforeach
     
    
</table>