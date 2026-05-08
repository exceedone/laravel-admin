<?php

namespace Encore\Admin\Form\Field;

use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait ImageField
{
    /**
     * Intervention calls.
     *
     * @var array<mixed>
     */
    protected $interventionCalls = [];

    /**
     * Thumbnail settings.
     *
     * @var array<mixed>
     */
    protected $thumbnails = [];

    /**
     * Default directory for file to upload.
     *
     * @return mixed
     */
    public function defaultDirectory()
    {
        return config('admin.upload.directory.image');
    }

    /**
     * Execute Intervention calls.
     *
     * @param string $target
     *
     * @return mixed
     */
    public function callInterventionMethods($target)
    {
        if (!empty($this->interventionCalls)) {
            $image = InterventionImage::read($target);

            foreach ($this->interventionCalls as $call) {
                $image = call_user_func_array(
                    [$image, $call['method']],
                    $call['arguments']
                );
            }

            $image->save($target);
        }

        return $target;
    }

    /**
     * Call intervention methods.
     *
     * @param string $method
     * @param array<mixed>  $arguments
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function __call($method, $arguments)
    {
        if (static::hasMacro($method)) {
            return $this;
        }

        if (!class_exists(\Intervention\Image\ImageManager::class)) {
            throw new \Exception('To use image handling and manipulation, please install [intervention/image] first.');
        }

        $this->interventionCalls[] = [
            'method'    => $method,
            'arguments' => $arguments,
        ];

        return $this;
    }

    /**
     * Render a image form field.
     */
    public function render()
    {
        $this->options(['allowedFileTypes' => ['image'], 'msgPlaceholder' => trans('admin.choose_image')]);

        return parent::render();
    }

    /**
     * @param string|array<mixed> $name
     * @param int          $width
     * @param int          $height
     *
     * @return $this
     */
    public function thumbnail($name, int $width = null, int $height = null)
    {
        if (func_num_args() == 1 && is_array($name)) {
            foreach ($name as $key => $size) {
                if (count($size) == 2) {
                    $this->thumbnails[$key] = $size;
                }
            }
        } elseif (func_num_args() == 3) {
            $this->thumbnails[$name] = [$width, $height];
        }

        return $this;
    }

    /**
     * Destroy original thumbnail files.
     *
     * @return void.
     */
    public function destroyThumbnail()
    {
        if ($this->retainable) {
            return;
        }

        foreach ($this->thumbnails as $name => $_) {
            // We need to get extension type ( .jpeg , .png ...)
            $ext = pathinfo($this->original, PATHINFO_EXTENSION);

            // We remove extension from file name so we can append thumbnail type
            $path = Str::replaceLast('.'.$ext, '', $this->original);

            // We merge original name + thumbnail name + extension
            $path = $path.'-'.$name.'.'.$ext;

            if ($this->storage->exists($path)) {
                $this->storage->delete($path);
            }
        }
    }

    /**
     * Upload file and delete original thumbnail files.
     *
     * @param UploadedFile $file
     *
     * @return $this
     */
    protected function uploadAndDeleteOriginalThumbnail(UploadedFile $file)
    {
        foreach ($this->thumbnails as $name => $size) {
            // We need to get extension type ( .jpeg , .png ...)
            $ext = pathinfo($this->name, PATHINFO_EXTENSION);

            // We remove extension from file name so we can append thumbnail type
            $path = Str::replaceLast('.'.$ext, '', $this->name);

            // We merge original name + thumbnail name + extension
            $path = $path.'-'.$name.'.'.$ext;

            /** @var \Intervention\Image\Image $image */
            $image = InterventionImage::read($file);

            // Resize image preserving aspect ratio and pad canvas to exact dimensions with white background.
            $image->contain($size[0], $size[1], 'ffffff');

            $encoded = (string) $image->encodeByPath($path);

            if (!is_null($this->storagePermission)) {
                $this->storage->put("{$this->getDirectory()}/{$path}", $encoded, $this->storagePermission);
            } else {
                $this->storage->put("{$this->getDirectory()}/{$path}", $encoded);
            }
        }

        $this->destroyThumbnail();

        return $this;
    }
}
