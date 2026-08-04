<?php

namespace Encore\Admin\Form\Field;

use Illuminate\Support\Str;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image as InterventionImage;
use Intervention\Image\ImageManagerStatic;
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
            $image = ImageManagerStatic::make($target);

            foreach ($this->interventionCalls as $call) {
                /** @var callable $callable */
                // @phpstan-ignore-next-line The value is always an array at runtime (and 1 more mixed-type assumption on this line)
                $callable = [$image, $call['method']];
                /** @var \Intervention\Image\Image $result */
                $result = call_user_func_array(
                    $callable,
                    // @phpstan-ignore-next-line The value is always an array at runtime (and 3 more mixed-type assumptions on this line)
                    $call['arguments']
                );
                $result->save($target);
            }
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

        if (!class_exists(ImageManagerStatic::class)) {
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
                // @phpstan-ignore-next-line $value is always array|Countable at runtime (and 1 more mixed-type assumption on this line)
                if (count($size) == 2) {
                    $this->thumbnails[$key] = $size;
                }
            }
        } elseif (func_num_args() == 3) {
            /** @phpstan-ignore-next-line Possibly invalid array key type array|string. */
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
            // @phpstan-ignore-next-line $path is always string at runtime (and 1 more mixed-type assumption on this line)
            $ext = pathinfo($this->original, PATHINFO_EXTENSION);

            // We remove extension from file name so we can append thumbnail type
            // @phpstan-ignore-next-line $subject is always string at runtime (and 1 more mixed-type assumption on this line)
            $path = Str::replaceLast('.'.$ext, '', $this->original);

            // We merge original name + thumbnail name + extension
            $path = $path.'-'.$name.'.'.$ext;

            /** @phpstan-ignore-next-line Cannot call method exists() on Illuminate\Filesystem\FilesystemAdapter|string. */
            if ($this->storage->exists($path)) {
                /** @phpstan-ignore-next-line Cannot call method delete() on Illuminate\Filesystem\FilesystemAdapter|string. */
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
            // @phpstan-ignore-next-line $path is always string at runtime (and 1 more mixed-type assumption on this line)
            $ext = pathinfo($this->name, PATHINFO_EXTENSION);

            // We remove extension from file name so we can append thumbnail type
            // @phpstan-ignore-next-line $subject is always string at runtime (and 1 more mixed-type assumption on this line)
            $path = Str::replaceLast('.'.$ext, '', $this->name);

            // We merge original name + thumbnail name + extension
            $path = $path.'-'.$name.'.'.$ext;

            /** @var \Intervention\Image\Image $image */
            $image = InterventionImage::make($file);

            // Resize image with aspect ratio
            // @phpstan-ignore-next-line The value is always an array at runtime (and 7 more mixed-type assumptions on this line)
            $image->resize($size[0], $size[1], function (Constraint $constraint) {
                $constraint->aspectRatio();
            // @phpstan-ignore-next-line The value is always an array at runtime (and 7 more mixed-type assumptions on this line)
            })->resizeCanvas($size[0], $size[1], 'center', false, '#ffffff');

            if (!is_null($this->storagePermission)) {
                /** @phpstan-ignore-next-line Cannot call method put() on Illuminate\Filesystem\FilesystemAdapter|string. */
                $this->storage->put("{$this->getDirectory()}/{$path}", $image->encode(), $this->storagePermission);
            } else {
                /** @phpstan-ignore-next-line Cannot call method put() on Illuminate\Filesystem\FilesystemAdapter|string. */
                $this->storage->put("{$this->getDirectory()}/{$path}", $image->encode());
            }
        }

        $this->destroyThumbnail();

        return $this;
    }
}
