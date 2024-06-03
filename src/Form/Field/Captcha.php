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
        $this->form = $form;

        $this->form->ignore($this->column);

        return $this;
    }

    public function render()
    {
        $this->script = <<<EOT

$('#{$this->column}-captcha').click(function () {
    $(this).attr('src', $(this).attr('src')+'?'+Math.random());
});

EOT;

        return parent::render();
    }
}
