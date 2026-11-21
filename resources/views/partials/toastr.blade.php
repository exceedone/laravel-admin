@if(Session::has('toastr'))
    @php
        $toastr     = Session::get('toastr');
        $type       = \Illuminate\Support\Arr::get($toastr->get('type'), 0, 'success');
        // only toastr's own methods; the message is text, written as JSON
        $type       = in_array($type, ['success', 'error', 'warning', 'info'], true) ? $type : 'info';
        $message    = (string)\Illuminate\Support\Arr::get($toastr->get('message'), 0, '');
        $options    = json_encode($toastr->get('options', []), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    @endphp
    <script>
        $(function () {
            toastr[@json($type)](@json($message, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT), null, {!! $options !!});
        });
    </script>
@endif