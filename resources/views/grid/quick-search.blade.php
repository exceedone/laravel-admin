<form action="{!! $action !!}" pjax-container style="display: inline-block;">
    <div class="input-group input-group-sm d-none d-md-flex flex-nowrap">
        <input type="text" name="{{ $key }}" class="form-control" style="width: 200px;" value="{{ $value }}">

        <div class="input-group-btn" style="display: inline-block;">
            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>