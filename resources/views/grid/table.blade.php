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
                <tr {!! $grid->getHeaderAttributes() !!}>
                    @foreach($grid->visibleColumns() as $column)
                    <th class="column-{!! $column->getName() !!}" {!! $column->getHeaderAttributes() !!}>{{$column->getLabel()}}{!! $column->sorter() !!}{!! $column->help() !!}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @if($grid->isLazyLoad())
                <tr class="lazy-grid-placeholder">
                    <td colspan="{{ count($grid->visibleColumnNames()) }}" data-lazy-grid-rows-url="{{ request()->fullUrlWithQuery(['lazy_grid_rows' => 1]) }}" data-lazy-grid-error="{{ trans('admin.failed') }}">
                        <i class="fa fa-spinner fa-spin lazy-grid-loading"></i>
                        <span class="lazy-grid-error text-danger" role="alert" style="display:none"></span>
                        <button type="button" class="btn btn-default btn-sm lazy-grid-retry" style="display:none"><i class="fa fa-refresh"></i> {{ trans('admin.refresh') }}</button>
                    </td>
                </tr>
                @else
                    @include('admin::grid.rows')
                @endif
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
