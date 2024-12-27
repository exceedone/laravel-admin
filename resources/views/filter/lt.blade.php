<div class="form-group row">
    <label class="col-sm-2 control-label col-form-label">{{$label}}&nbsp;(<)</label>
    <div class="col-sm-8">
        @include($presenter->view())
    </div>
    @if ($nullcheck)
    <div class="col-sm-2" style="padding-top:3px;">
        <input type="checkbox" class="isnull-{{$column}}" name="isnull-{{$column}}" value="1" {{$isnull}} />&nbsp;{{ trans('admin.empty') }}&nbsp;&nbsp;
    </div>
    @endif
</div>