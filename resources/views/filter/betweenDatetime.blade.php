<div class="form-group">
    <label class="col-sm-2 control-label">{{$label}}</label>
    <div class="col-sm-8">
        <div class="input-group input-group-sm">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control" id="{{$id['start']}}" placeholder="{{$label}}" autocomplete="off" name="{{$name['start']}}" value="{{ request($name['start'], \Illuminate\Support\Arr::get($value, 'start')) }}">
            <span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span>
            <input type="text" class="form-control" id="{{$id['end']}}" placeholder="{{$label}}" autocomplete="off" name="{{$name['end']}}" value="{{ request($name['end'], \Illuminate\Support\Arr::get($value, 'end')) }}">
        </div>
    </div>
    @if ($nullcheck)
    <div class="col-sm-2" style="padding-top:3px;">
        <input type="checkbox" class="isnull-{{$column}}" name="isnull-{{$column}}" value="1" {{$isnull}} />&nbsp;{{ trans('admin.empty') }}&nbsp;&nbsp;
    </div>
    @endif
</div>