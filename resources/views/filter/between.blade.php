<div class="form-group row">
    <label class="col-sm-2 control-label col-form-label">{{$label}}</label>
    <div class="col-sm-8">
        <div class="input-group input-group-sm">
            <input type="text" class="form-control" placeholder="{{$label}}" name="{{$name['start']}}" value="{{ request($name['start'], \Illuminate\Support\Arr::get($value, 'start')) }}">
            <span class="input-group-text" style="border-left: 0; border-right: 0;">-</span>
            <input type="text" class="form-control" placeholder="{{$label}}" name="{{$name['end']}}" value="{{ request($name['end'], \Illuminate\Support\Arr::get($value, 'end')) }}">
        </div>
    </div>
    @if ($nullcheck)
    <div class="col-sm-2" style="padding-top:3px;">
        <input type="checkbox" class="isnull-{{$column}}" name="isnull-{{$column}}" value="1" {{$isnull}} />&nbsp;{{ trans('admin.empty') }}&nbsp;&nbsp;
    </div>
    @endif
</div>