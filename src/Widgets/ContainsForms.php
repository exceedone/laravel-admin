<?php

namespace OpenAdmin\Admin\Widgets;

trait ContainsForms
{
    /**
     * @var string
     */
    protected $activeName = 'active';

    /**
     * @param array<mixed> $forms
     * @param mixed|null $active
     * @return $this
     */
    public static function forms($forms, $active = null)
    {
        /** @phpstan-ignore-next-line Unsafe usage of new static().   */
        $tab = new static();

        return $tab->buildTabbedForms($forms, $active);
    }

    /**
     * @param array<mixed> $forms
     * @param null|mixed $active
     * @return $this
     */
    protected function buildTabbedForms($forms, $active = null)
    {
        $active = $active ?: request($this->activeName);

        if (!isset($forms[$active])) {
            $active = key($forms);
        }

        foreach ($forms as $name => $class) {
            if (!is_subclass_of($class, Form::class)) {
                admin_error("Class [{$class}] must be a sub-class of [OpenAdmin\Admin\Widgets\Form].");
                continue;
            }

            /** @var Form $form */
            $form = app()->make($class);

            if ($name == $active) {
                $this->add($form->title, $form->unbox(), true);
            } else {
                $this->addLink($form->title, $this->getTabUrl($name));
            }
        }

        return $this;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getTabUrl($name)
    {
        $query = [$this->activeName => $name];

        return request()->fullUrlWithQuery($query);
    }
}
