@include('admin::grid.rows')
<template data-lazy-grid-fragment></template>
@if(count($script))
    @include('admin::partials.script')
@endif
