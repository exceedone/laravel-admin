<?php

namespace OpenAdmin\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;

class Carousel extends Widget implements Renderable
{
    /**
     * @var string
     */
    protected $view = 'admin::widgets.carousel';

    /**
     * @var array<mixed>
     */
    protected $items;

    /**
     * @var string
     */
    protected $title = 'Carousel';

    /**
     * Carousel constructor.
     *
     * @param array<mixed>|Collection<int|string, mixed>|mixed $items
     */
    public function __construct($items = [])
    {
        $this->items = $items;

        $this->id('carousel-'.uniqid());
        $this->class('carousel slide');
        $this->offsetSet('data-ride', 'carousel');
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return void
     */
    public function title($title)
    {
        $this->title = $title;
    }

    /**
     * Render Carousel.
     *
     * @return string
     */
    public function render()
    {
        $variables = [
            'items'      => $this->items,
            'title'      => $this->title,
            'attributes' => $this->formatAttributes(),
            'id'         => $this->id,
            'width'      => $this->width ?: 300,
            'height'     => $this->height ?: 200,
        ];

        return view($this->view, $variables)->render();
    }
}
