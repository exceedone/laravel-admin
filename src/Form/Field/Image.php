<?php

namespace Encore\Admin\Form\Field;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Image extends File
{
    use ImageField;

    /**
     * {@inheritdoc}
     */
    protected $view = 'admin::form.file';

    /**
     *  Validation rules.
     *
     * @var string
     */
    protected $rules = 'image';

    /**
     * @param array|UploadedFile|null $image
     */
    public function prepare($image)
    {
        // If has $file is string, and has TMP_FILE_PREFIX, get $file
        if(is_string($image) && strpos($image, File::TMP_FILE_PREFIX) === 0 && $this->getTmp){
            $image = call_user_func($this->getTmp, $image);
        }

        if (request()->has(static::FILE_DELETE_FLAG)) {
            $this->destroy();
            return;
        }

        if(is_null($image)){
            return null;
        }

        $this->name = $this->getStoreName($image);

        $this->callInterventionMethods($image->getRealPath());

        $path = $this->uploadAndDeleteOriginal($image);

        $this->uploadAndDeleteOriginalThumbnail($image);

        return $path;
    }
    
    /**
     * Render file upload field.
     */
    public function render()
    {
        $this->filetype('image');
        return parent::render();
    }
}
