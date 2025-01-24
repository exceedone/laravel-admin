<div class="box-footer mt-3 border-top border-light py-3 d-flex" style="background-color: inherit;">

    {{ csrf_field() }}

    <div class="col-md-{{$width['label']}}">
    </div>

    <div class="col-md-{{$width['field']}}">

        @if(in_array('submit', $buttons))
        <div class="btn-group float-end">
            <button id="admin-submit" type="submit" class="btn btn-primary">{{ $submitLabel ?? trans('admin.submit') }}</button>
        </div>

        @foreach($submitRedirects as $redirect)
            <label class="float-end" style="margin: 5px 10px 0 0;">
                <input type="checkbox" class="after-submit" name="after-save" value="{{ array_get($redirect, 'value') }}" {{ $default_check == array_get($redirect, 'value') ? 'checked' : '' }}> {{ array_get($redirect, 'label') }}
            </label>
        @endforeach

        @endif

        @if(in_array('reset', $buttons))
        <div class="btn-group float-start">
            <button type="reset" class="btn btn-warning">{{ trans('admin.reset') }}</button>
        </div>
        @endif
    </div>
</div>