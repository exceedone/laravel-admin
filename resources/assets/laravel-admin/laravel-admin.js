$.fn.editable.defaults.params = function (params) {
    params._token = LA.token;
    params._editable = 1;
    params._method = 'PUT';
    return params;
};

$.fn.editable.defaults.error = function (data) {
    var msg = '';
    if (data.responseJSON.errors) {
        $.each(data.responseJSON.errors, function (k, v) {
            msg += v + "\n";
        });
    }
    return msg
};

toastr.options = {
    closeButton: true,
    progressBar: true,
    showMethod: 'slideDown',
    timeOut: 4000
};

$.pjax.defaults.timeout = 5000;
$.pjax.defaults.maxCacheLength = 0;
$(document).pjax('a:not(a[target="_blank"]):not(a[data-nopjax])', {
    container: '#pjax-container'
});

NProgress.configure({parent: '#app'});

$(document).on('pjax:timeout', function (event) {
    event.preventDefault();
})

$(document).on('submit', 'form[pjax-container]', function (event) {
    $.pjax.submit(event, '#pjax-container')
});

$(document).on("pjax:popstate", function () {

    $(document).one("pjax:end", function (event) {
        $(event.target).find("script[data-exec-on-popstate]").each(function () {
            $.globalEval(this.text || this.textContent || this.innerHTML || '');
        });
    });
});

$(document).on('pjax:send', function (xhr) {
    if (xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
        $submit_btn = $('form[pjax-container] :submit');
        if ($submit_btn) {
            $submit_btn.button('loading')
        }
    }
    NProgress.start();
});

$(document).on('pjax:complete', function (xhr) {
    if (xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
        $submit_btn = $('form[pjax-container] :submit');
        if ($submit_btn) {
            $submit_btn.button('reset')
        }
    }
    NProgress.done();
    $.admin.grid.selects = {};
});

/**
 * Marks a grid placeholder as failed, displays its localized error and enables retry.
 * Failed grids are skipped by the automatic queue until a manual retry is requested.
 *
 * @param {JQuery} $placeholder Placeholder row containing the loading and retry controls.
 * @returns {void}
 */
function failLazyGridRows($placeholder) {
    $placeholder.data('lazy-grid-failed', true);
    $placeholder.find('.lazy-grid-loading').hide();
    $placeholder.find('.lazy-grid-error').text($placeholder.data('lazy-grid-error')).show();
    $placeholder.find('.lazy-grid-retry').prop('disabled', false).show();
}

/**
 * Loads one grid's row fragment, serializing pending grids on the current page.
 *
 * When no placeholder is supplied, selects the next pending grid that has not failed.
 * Validates the fragment, ignores detached grids and runs row initialization scripts.
 * Restores the original tbody children if initialization fails and exposes retry.
 * Request completion advances the queue to the next pending grid.
 *
 * @param {JQuery} [$placeholder] Placeholder row to load; omitted to advance the queue.
 * @returns {void}
 */
function loadLazyGridRows($placeholder) {
    var $pending = $('[data-lazy-grid-rows-url]');
    if ($pending.filter(function () { return $(this).data('lazy-grid-loading'); }).length) {
        return;
    }

    $placeholder = $placeholder || $pending.filter(function () {
        return !$(this).data('lazy-grid-loading') && !$(this).data('lazy-grid-failed');
    }).first();
    var url = $placeholder.data('lazy-grid-rows-url');

    if (!url || $placeholder.data('lazy-grid-loading')) {
        return;
    }

    $placeholder.data('lazy-grid-loading', true).data('lazy-grid-failed', false);
    $placeholder.find('.lazy-grid-loading').show();
    $placeholder.find('.lazy-grid-error').hide();
    $placeholder.find('.lazy-grid-retry').prop('disabled', true).hide();
    $.get(url).done(function (rows) {
        var $tbody = $placeholder.closest('tbody');
        if (!$.contains(document, $tbody[0])) {
            return;
        }

        var $originalRows;
        try {
            var $fragment = $($.parseHTML(rows, document, true));
            var $marker = $fragment.filter('template[data-lazy-grid-fragment]');
            var $scripts = $fragment.filter('script');
            var $rows = $fragment.not('template[data-lazy-grid-fragment]').not('script');
            if ($marker.length !== 1 || $rows.filter(function () {
                return this.nodeType === 1 && this.tagName.toLowerCase() !== 'tr';
            }).length) {
                throw new Error('Invalid lazy-grid fragment.');
            }

            $originalRows = $tbody.children().detach();
            $tbody.append($rows);
            $scripts.each(function () {
                $.globalEval(this.text || this.textContent || this.innerHTML || '');
            });
        } catch (error) {
            if ($originalRows) {
                $tbody.empty().append($originalRows);
            }
            failLazyGridRows($placeholder);
            console.error('Unable to initialize lazy-grid rows.', error);
        }
    }).fail(function () {
        failLazyGridRows($placeholder);
    }).always(function () {
        $placeholder.data('lazy-grid-loading', false);
        loadLazyGridRows();
    });
}

/**
 * Schedules lazy rows after initial page display or PJAX navigation.
 * Uses an idle callback with a 1000 ms timeout, or a 500 ms timer fallback.
 *
 * @returns {void}
 */
function scheduleLazyGridRows() {
    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(function () { loadLazyGridRows(); }, { timeout: 1000 });
    } else {
        setTimeout(loadLazyGridRows, 500);
    }
}

$(document).on('pjax:end', scheduleLazyGridRows);
$(scheduleLazyGridRows);
$(document).on('click', '.lazy-grid-retry', function (event) {
    event.preventDefault();
    $(this).closest('[data-lazy-grid-rows-url]').data('lazy-grid-failed', false);
    loadLazyGridRows();
});

$(document).click(function () {
    $('.sidebar-form .dropdown-menu').hide();
});

$(function () {
    $('.sidebar-menu li:not(.treeview) > a').on('click', function () {
        var $parent = $(this).parent().addClass('active');
        $parent.siblings('.treeview.active').find('> a').trigger('click');
        $parent.siblings().removeClass('active').find('li').removeClass('active');
    });
    var menu = $('.sidebar-menu li > a[href="' + (location.pathname + location.search + location.hash) + '"]').parent().addClass('active');
    menu.parents('ul.treeview-menu').addClass('menu-open');
    menu.parents('li.treeview').addClass('active');

    $('[data-toggle="popover"]').popover();

    // Sidebar form autocomplete
    $('.sidebar-form .autocomplete').on('keyup focus', function () {
        var $menu = $('.sidebar-form .dropdown-menu');
        var text = $(this).val();

        if (text === '') {
            $menu.hide();
            return;
        }

        var regex = new RegExp(text, 'i');
        var matched = false;

        $menu.find('li').each(function () {
            if (!regex.test($(this).find('a').text())) {
                $(this).hide();
            } else {
                $(this).show();
                matched = true;
            }
        });

        if (matched) {
            $menu.show();
        }
    }).click(function(event){
        event.stopPropagation();
    });

    $('.sidebar-form .dropdown-menu li a').click(function (){
        $('.sidebar-form .autocomplete').val($(this).text());
    });
});

$(window).scroll(function() {
    if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
        $('#totop').fadeIn(500);
    } else {
        $('#totop').fadeOut(500);
    }
});

$('#totop').on('click', function (e) {
    e.preventDefault();
    $('html,body').animate({scrollTop: 0}, 500);
});

(function ($) {

    var Grid = function () {
        this.selects = {};
    };

    Grid.prototype.select = function (id) {
        this.selects[id] = id;
    };

    Grid.prototype.unselect = function (id) {
        delete this.selects[id];
    };

    Grid.prototype.selected = function () {
        var rows = [];
        $.each(this.selects, function (key, val) {
            rows.push(key);
        });

        return rows;
    };

    $.fn.admin = LA;
    $.admin = LA;
    $.admin.swal = swal;
    $.admin.toastr = toastr;
    $.admin.grid = new Grid();

    $.admin.reload = function () {
        $.pjax.reload('#pjax-container');
    };

    $.admin.redirect = function (url) {
        $.pjax({container:'#pjax-container', url: url });
    };

    $.admin.getToken = function () {
        return $('meta[name="csrf-token"]').attr('content');
    };

})(jQuery);
