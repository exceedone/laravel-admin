<?php

namespace OpenAdmin\Admin\Widgets\Grid\Tools;

class BatchDelete extends BatchAction
{
    /**
     * @param string $title
     */
    public function __construct($title)
    {
        $this->title = $title;
    }

    /**
     * Script of batch delete action.
     */
    public function script()
    {
        $url = url($this->resource);
        $trans = [
            'delete_confirm' => trans('admin.delete_confirm'),
            'confirm'        => trans('admin.confirm'),
            'cancel'         => trans('admin.cancel'),
        ];

        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {

    swal({
        title: "{$trans['delete_confirm']}",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "{$trans['confirm']}",
        showLoaderOnConfirm: true,
        allowOutsideClick: false,
        cancelButtonText: "{$trans['cancel']}",
        preConfirm: function() {
            $('.swal2-cancel').hide();
            return new Promise(function(resolve) {
                $.ajax({
                    method: 'post',
                    url: '{$url}/' + $.admin.grid.selected().join(),
                    data: {
                        _method:'delete',
                        _token:'{$this->getToken()}'
                    },
                    success: function (data) {
                        $.pjax.reload('#pjax-container');

                        resolve(data);
                    }
                });
            });
        }
    }).then(function(result) {
        var data = result.value;
        if (typeof data === 'object') {
            if (data.status) {
                swal(data.message, '', 'success');
            } else {
                swal(data.message, '', 'error');
            }
        }
    });
});

EOT;
    }
}
