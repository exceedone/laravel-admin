<?php

namespace OpenAdmin\Admin\Widgets\Navbar;

use OpenAdmin\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;

class RefreshButton implements Renderable
{
    public function render()
    {
        $message = __('admin.refresh_succeeded');

        $script = <<<SCRIPT
$('.refresh-button').off('click').on('click', function() {
    $.admin.reload();
    $.admin.toastr.success('{$message}', '', {positionClass:"toast-top-center"});
});
SCRIPT;

        Admin::script($script);

        return <<<'EOT'
<li>
    <a href="javascript:void(0);" class="refresh-button hidden-xs">
      <i class="fa fa-refresh"></i>
    </a>
</li>
EOT;
    }
}
