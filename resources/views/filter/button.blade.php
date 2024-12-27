<div class="btn-group" style="margin-right: 5px" data-toggle="buttons">
    <label class="btn d-flex text-nowrap btn-primary btn-sm btn-dropbox {{ $btn_class }} {{ $expand ? 'active' : '' }}" title="{{ trans('admin.filter') }}" data-loading-text="<i class='fa fa-spinner fa-spin '></i>">
        <input type="checkbox" style="display: none"><i class="fa fa-filter"></i><span class="d-none d-sm-block">&nbsp;&nbsp;{{ trans('admin.filter') }}</span>
    </label>

    @if($scopes->isNotEmpty())
    <button type="button" class="btn btn-primary btn-sm btn-dropbox dropdown-toggle" data-toggle="dropdown">

        <span>{{ $current_label }}</span>
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        @foreach($scopes as $scope)
            {!! $scope->render() !!}
        @endforeach
        <li role="separator" class="divider"></li>
        <li><a href="{{ $url_no_scopes }}">{{ trans('admin.cancel') }}</a></li>
    </ul>
    @endif
</div>