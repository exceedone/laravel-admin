<?php

namespace OpenAdmin\Admin\Grid\Filter\Presenter;

use OpenAdmin\Admin\Grid\Filter\AbstractFilter;

abstract class Presenter
{
    /**
     * @var AbstractFilter
     */
    protected $filter;

    /**
     * Get filter.
     * @return AbstractFilter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set parent filter.
     *
     * @param AbstractFilter $filter
     *
     * @return void
     */
    public function setParent(AbstractFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @see https://stackoverflow.com/questions/19901850/how-do-i-get-an-objects-unqualified-short-class-name
     *
     * @return string
     */
    public function view() : string
    {
        $reflect = new \ReflectionClass(get_called_class());

        return 'admin::filter.'.strtolower($reflect->getShortName());
    }

    /**
     * Set default value for filter.
     *
     * @param array<mixed>|string|null $default
     *
     * @return $this
     */
    public function default($default)
    {
        $this->filter->default($default);

        return $this;
    }

    /**
     * Blade template variables for this presenter.
     *
     * @return array<mixed>
     */
    public function variables() : array
    {
        return [];
    }
}
