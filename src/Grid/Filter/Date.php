<?php

namespace OpenAdmin\Admin\Grid\Filter;

class Date extends AbstractFilter
{
    /**
     * {@inheritdoc}
     * @var string
     */
    protected $query = 'whereDate';

    /**
     * @var string
     */
    protected $fieldName = 'date';

    /**
     * {@inheritdoc}
     * @param mixed $column
     * @param  string $label
     */
    public function __construct($column, $label = '')
    {
        parent::__construct($column, $label);

        $this->{$this->fieldName}();
    }
}
