<?php

namespace Encore\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;

/**
 * @phpstan-consistent-constructor
 */
class MultipleSteps implements Renderable
{
    /**
     * @var int|string
     */
    protected $current;

    /**
     * @var array<mixed>
     */
    protected $steps = [];

    /**
     * @var string
     */
    protected $stepName = 'step';

    /**
     * MultipleSteps constructor.
     *
     * @param array<mixed> $steps
     * @param null|int|string  $current
     */
    public function __construct($steps = [], $current = null)
    {
        $this->steps = $steps;

        // @phpstan-ignore-next-line Current step may be null from constructor parameter
        $this->current = $this->resolveCurrentStep($steps, $current);
    }

    /**
     * @param array<mixed> $steps
     * @param null  $current
     *
     * @return static
     */
    public static function make($steps, $current = null): self
    {
        return new static($steps, $current);
    }

    /**
     * @param array<mixed> $steps
     * @param string|int $current
     *
     * @return string|int
     */
    protected function resolveCurrentStep($steps, $current)
    {
        $current = $current ?: request($this->stepName, 0);

        if (!isset($steps[$current])) {
            $current = key($steps);
        }

        // @phpstan-ignore-next-line Return value is always int|string at runtime
        return $current;
    }

    /**
     * @return string|null|void
     */
    public function render()
    {
        $class = $this->steps[$this->current];

        // @phpstan-ignore-next-line $object_or_class is always object|string at runtime
        if (!is_subclass_of($class, StepForm::class)) {
            // @phpstan-ignore-next-line $class is always castable to string at runtime
            admin_error("Class [{$class}] must be a sub-class of [Encore\Admin\Widgets\StepForm].");

            return;
        }

        /** @var StepForm $step */
        $step = new $class();

        return $step
            ->setSteps(array_keys($this->steps))
            ->setCurrent($this->current)
            ->render();
    }
}
