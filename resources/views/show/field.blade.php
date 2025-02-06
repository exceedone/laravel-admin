<div class="{{$form_group}} row">
    <label class="{{$width['label']}} col-form-label text-right">{{ $label }}</label>
    <div class="{{$width['field']}}">
        @if($wrapped)
        <div class="box box-solid box-default no-margin box-show">
            <!-- /.box-header -->
            <div class="box-body">
                @if($escape)
                    {{ $content }}
                @else
                    {!! $content !!}
                @endif
            </div><!-- /.box-body -->
        </div>
        @else
            @if($escape)
                {{ $content }}
            @else
                {!! $content !!}
            @endif
        @endif
    </div>
</div>