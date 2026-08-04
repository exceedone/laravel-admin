<?php

namespace Encore\Admin\Form\Field;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class MultipleImage extends MultipleFile
{
    use ImageField;

    /**
     * {@inheritdoc}
     */
    protected $view = 'admin::form.multiplefile';

    /**
     *  Validation rules.
     *
     * @var string
     */
    protected $rules = 'image';

    /**
     * Prepare for each file.
     *
     * @param UploadedFile $image
     *
     * @return mixed|string
     */
    protected function prepareForeach(UploadedFile $image = null)
    {
        $this->name = $this->getStoreName($image);

        // @phpstan-ignore-next-line Image is guaranteed to be set when preparing upload
        $this->callInterventionMethods($image->getRealPath());

        return tap($this->upload($image), function () { // @phpstan-ignore-line Image is guaranteed to be set when preparing upload
            $this->name = null;
        });
    }
}
