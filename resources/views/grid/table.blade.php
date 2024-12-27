<div class="card p-0">
    @if(isset($title))
        <div class="container-fluid card-header no-border col d-flex justify-content-start">
            <h3 class="box-title"> {{ $title }}</h3>
        </div>
    @endif

    @if ($grid->showTools() || $grid->showExportBtn() || $grid->showCreateBtn())
        <div class="container-fluid card-header no-border">
            <div class="row align-items-center">
                <div class="pull-right">
                    {!! $grid->renderColumnSelector() !!}
                    {!! $grid->renderExportButton() !!}
                    {!! $grid->renderCreateButton() !!}
                </div>
                @if ($grid->showTools())
                    @include('admin::grid.tools')
                @endif
            </div>


        </div>
    @endif

    {!! $grid->renderFilter() !!}

    {!! $grid->renderHeader() !!}

    <!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover" id="{{ $grid->tableID }}">
            <thead>
                <tr {!! $grid->getHeaderAttributes() !!}>
                    @foreach($grid->visibleColumns() as $column)
                        <th class="column-{!! $column->getName() !!}" {!! $column->getHeaderAttributes() !!}>
                            {{$column->getLabel()}}{!! $column->sorter() !!}{!! $column->help() !!}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach($grid->rows() as $row)
                    <tr {!! $row->getRowAttributes() !!}>
                        @foreach($grid->visibleColumnNames() as $name)
                            <td {!! $row->getColumnAttributes($name) !!} class="{!! $row->getColumnClasses($name) !!}">
                                {!! $row->column($name) !!}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>

            {!! $grid->renderTotalRow() !!}

        </table>

    </div>

    {!! $grid->renderFooter() !!}

    <div class="navbar navbar-light bg-white py-3 px-4">
        {!! $grid->paginator() !!}
    </div>
    <!-- /.box-body -->
</div>