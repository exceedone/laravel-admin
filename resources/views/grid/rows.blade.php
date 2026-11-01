@foreach($grid->rows() as $row)
<tr {!! $row->getRowAttributes() !!}>
    @foreach($grid->visibleColumnNames() as $name)
    <td {!! $row->getColumnAttributes($name) !!} class="{!! $row->getColumnClasses($name) !!}">
        {!! $row->column($name) !!}
    </td>
    @endforeach
</tr>
@endforeach