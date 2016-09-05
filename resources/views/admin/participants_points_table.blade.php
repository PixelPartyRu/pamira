<table{!! $dg->buildAttributes() !!}>
	@if ($dg->rowCount())
        
    		<thead>
			<tr>
				@foreach ($dg->columns as $column)
				        <th{!! $column->buildAttributes() !!}>
				        	@if ($column->orderby)
							@if ($dg->onOrderby($column->orderby_field, 'asc'))
						                <span class="glyphicon glyphicon-arrow-up"></span>
					                @else
						                <a href="{!! $dg->orderbyLink($column->orderby_field,'asc') !!}">
						                        <span class="glyphicon glyphicon-arrow-up"></span>
						                </a>
					                @endif
					                @if ($dg->onOrderby($column->orderby_field, 'desc'))
						                <span class="glyphicon glyphicon-arrow-down"></span>
					                @else
						                <a href="{!! $dg->orderbyLink($column->orderby_field,'desc') !!}">
						                        <span class="glyphicon glyphicon-arrow-down"></span>
						                </a>
					                @endif
						@endif
						        {!! $column->label !!}
					</th>
				@endforeach
			</tr>
		</thead>
		<tbody>
                    
			@foreach ($dg->rows as $row)
			        <tr{!! $row->buildAttributes() !!}>
				        @foreach ($row->cells as $cell)
                                        
					        <td{!! $cell->buildAttributes() !!}>
                                                    @if($cell->name == "points")
                                                    

                                                    <input class="points_info" data-pid="{{$row->data->id}}" type="number" min="0" name="points" value="{!! $cell->value !!}">
                                                    @else
                                                    {!! $cell->value !!}
                                                    @endif
                                                </td>
			                @endforeach
			        </tr>
			@endforeach
		</tbody>
	@else
		<h4>{{ Lang::get('rapyd::rapyd.empty_list') }}</h4>
	@endif
</table>

