<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Form;

class Captcha extends Text
{
    protected $rules = 'required|captcha';

    protected $view = 'admin::form.captcha';

    /**
     * @param $column
     * @param $arguments
     * @throws \Exception
     * @phpstan-ignore-next-line
     */
    public function __construct($column, $arguments = [])
    {
        if (!class_exists(\Mews\Captcha\Captcha::class)) {
            throw new \Exception('To use captcha field, please install [mews/captcha] first.');
        }

        $this->column = '__captcha__';
        $this->label = trans('admin.captcha');
    }

    public function setForm($form = null)
    {
        /** @phpstan-ignore-next-line Property Encore\Admin\Form\Field::$form (Encore\Admin\Form|null) does not accept Encore\Admin\Form|Encore\Admin\Widgets\Form|null. */
        $this->form = $form;

        /** @phpstan-ignore-next-line Call to an undefined method Encore\Admin\Form|Encore\Admin\Widgets\Form::ignore(). */
        $this->form->ignore($this->column);

        return $this;
    }

    public function render()
    {
        /** @phpstan-ignore-next-line argument.type */
        $this->script = <<<EOT

$('#{$this->column}-captcha').click(function () {
    $(this).attr('src', $(this).attr('src')+'?'+Math.random());
});

EOT;

        return parent::render();
    }
}
