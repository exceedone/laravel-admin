@if($help)
<span class="help-block d-block pt-2">
    <i class="fa {{ \Illuminate\Support\Arr::get($help, 'icon') }}"></i>&nbsp;{!! \Illuminate\Support\Arr::get($help, 'text') !!}
</span>
@endif