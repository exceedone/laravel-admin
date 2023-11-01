<div class="box">
    @if(isset($title))
    <div class="box-header with-border">
        <h3 class="box-title"> {{ $title }}</h3>
    </div>
    @endif

    @if ( $grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn() )
    <div class="box-header with-border">
        <div class="pull-right">
            {!! $grid->renderColumnSelector() !!}
            {!! $grid->renderExportButton() !!}
            {!! $grid->renderCreateButton() !!}
        </div>
        @if ( $grid->showTools() )
        @include('admin::grid.tools')
        @endif
    </div>
    @endif

    {!! $grid->renderFilter() !!}

    {!! $grid->renderHeader() !!}

    <!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover" id="{{ $grid->tableID }}">
            <thead>
                <tr>
                    @foreach($grid->visibleColumns() as $column)
                    <th class="column-{!! $column->getName() !!}">{{$column->getLabel()}}{!! $column->sorter() !!}{!! $column->help() !!}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach($grid->rows() as $row)
                <tr {!! $row->getRowAttributes() !!}>
                    @php
                        $i = 1;
                    @endphp
                    @foreach($grid->visibleColumnNames() as $name)
                    <td {!! $row->getColumnAttributes($name) !!} class="{!! $row->getColumnClasses($name) !!}">
                        {!! $row->column($name) !!}
                    </td>
                    @php
                        $i++;
                    @endphp
                    @endforeach
                </tr>
                <tr>
                    <td></td>
                    <td colspan="{{$i-1}}">
                        <div class="input-group" style="width: 100%;">
                            <input form="{{$input_of_form}}" type="text" name="input_text_row_{{$row->id}}" class="form-control" />
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>

            {!! $grid->renderTotalRow() !!}

        </table>

    </div>

    {!! $grid->renderFooter() !!}

    <div class="box-footer table-footer clearfix">
        {!! $grid->paginator() !!}
    </div>
    <!-- /.box-body -->
</div>
