<?php

namespace OpenAdmin\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;

/**
 * @property string $class
 */
class Box extends Widget implements Renderable
{
    /**
     * @var string
     */
    protected $view = 'admin::widgets.box';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $content = 'here is the box content.';

    /**
     * @var string
     */
    protected $footer = '';

    /**
     * @var array<mixed>
     */
    protected $tools = [];

    /**
     * @var string
     */
    protected $script;

    /**
     * Box constructor.
     *
     * @param string $title
     * @param string|Form $content
     * @param string $footer
     */
    public function __construct($title = '', $content = '', $footer = '')
    {
        if ($title) {
            $this->title($title);
        }

        if ($content) {
            $this->content($content);
        }

        if ($footer) {
            $this->footer($footer);
        }

        $this->class('box');
    }

    /**
     * Set box content.
     *
     * @param Renderable|string $content
     *
     * @return $this
     */
    public function content($content)
    {
        if ($content instanceof Renderable) {
            $this->content = $content->render();
        } else {
            $this->content = (string) $content;
        }

        return $this;
    }

    /**
     * Set box footer.
     *
     * @param Renderable|string $footer
     *
     * @return $this
     */
    public function footer($footer)
    {
        if ($footer instanceof Renderable) {
            $this->footer = $footer->render();
        } else {
            $this->footer = (string) $footer;
        }

        return $this;
    }

    /**
     * Set box title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set box as collapsable.
     *
     * @return $this
     */
    public function collapsable()
    {
        $this->tools[] =
            '<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>';

        return $this;
    }

    /**
     *  Set box body scrollable.
     *
     * @param array<mixed> $options
     * @param string $nodeSelector
     *
     * @return $this
     */
    public function scrollable($options = [], $nodeSelector = '')
    {
        $this->id = uniqid('box-slim-scroll-');
        $scrollOptions = json_encode($options);
        $nodeSelector = $nodeSelector ?: '.box-body';

        $this->script = <<<SCRIPT
$("#{$this->id} {$nodeSelector}").slimScroll({$scrollOptions});
SCRIPT;

        return $this;
    }

    /**
     * Set box as removable.
     *
     * @return $this
     */
    public function removable()
    {
        $this->tools[] =
            '<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>';

        return $this;
    }

    /**
     * Set box tools.
     * @param mixed $html
     *
     * @return $this
     */
    public function tools($html)
    {
        $this->tools[] = $html;

        return $this;
    }

    /**
     * Set box style.
     *
     * @param string|array<mixed> $styles
     *
     * @return $this|Box
     */
    public function style($styles)
    {
        if (is_string($styles)) {
            return $this->style([$styles]);
        }

        $styles = array_map(function ($style) {
            return 'box-'.$style;
        }, $styles);

        $this->class = $this->class.' '.implode(' ', $styles);

        return $this;
    }

    /**
     * Add `box-solid` class to box.
     *
     * @return $this|Box
     */
    public function solid()
    {
        return $this->style('solid');
    }

    /**
     * Variables in view.
     *
     * @return array<string, mixed>
     */
    protected function variables()
    {
        return [
            'title'      => $this->title,
            'content'    => $this->content,
            'footer'     => $this->footer,
            'tools'      => $this->tools,
            'attributes' => $this->formatAttributes(),
            'script'     => $this->script,
        ];
    }

    /**
     * Render box.
     *
     * @return string
     */
    public function render()
    {
        return view($this->view, $this->variables())->render();
    }
}
