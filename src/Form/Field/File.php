<?php

namespace OpenAdmin\Admin\Form\Field;

use OpenAdmin\Admin\Form\Field;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Closure;

class File extends Field
{
    use UploadField;

    /**
     * Tmp file prefix name. If file name is this prefix, get from tmp file.
     * @var string
     */
    const TMP_FILE_PREFIX = 'tmp:';

    /**
     * Css.
     *
     * @var array<string>
     */
    protected static $css = [
        // '/vendor/open-admin/bootstrap-fileinput/css/fileinput.min.css?v=4.5.2',
    ];

    /**
     * Js.
     *
     * @var array<string>
     */
    protected static $js = [
        '/vendor/open-admin/bootstrap-fileinput/js/plugins/canvas-to-blob.min.js',
        // '/vendor/open-admin/bootstrap-fileinput/js/fileinput.min.js?v=4.5.2',
    ];

    /**
     * Caption.
     *
     * @var \Closure|null
     */
    protected $caption = null;

    /**
     * file Index.
     *
     * @var \Closure|null
     */
    protected $fileIndex = null;


    /**
     * Create a new File instance.
     *
     * @param string $column
     * @param array<mixed>  $arguments
     */
    public function __construct($column, $arguments = [])
    {
        $this->initStorage();

        parent::__construct($column, $arguments);
    }

    /**
     * Default directory for file to upload.
     *
     * @return mixed
     */
    public function defaultDirectory()
    {
        return config('admin.upload.directory.file');
    }

    /**
     * {@inheritdoc}
     * @param array<mixed> $input
     */
    public function getValidator(array $input)
    {
        if (request()->has(static::FILE_DELETE_FLAG)) {
            return false;
        }

        if ($this->validator) {
            return $this->validator->call($this, $input);
        }

        /*
         * If has original value, means the form is in edit mode,
         * then remove required rule from rules.
         */
        if ($this->original()) {
            $this->removeRule('required');
        }

        /*
         * Make input data validatable if the column data is `null`.
         */
        if (Arr::has($input, $this->column) && is_null(Arr::get($input, $this->column))) {
            $input[$this->column] = '';
        }

        $rules = $attributes = [];

        if (!$fieldRules = $this->getRules()) {
            return false;
        }

        $rules[$this->column] = $fieldRules;
        $attributes[$this->column] = $this->label;

        return \validator($input, $rules, $this->getValidationMessages(), $attributes);
    }

    /**
     * Prepare for saving.
     *
     * @param UploadedFile|array<mixed>|string $file
     *
     * @return mixed|string|void
     */
    public function prepare($file)
    {
        // If has $file is string, and has TMP_FILE_PREFIX, get $file
        if (is_string($file) && strpos($file, static::TMP_FILE_PREFIX) === 0 && $this->getTmp) {
            $file = call_user_func($this->getTmp, $file);
        }

        if (request()->has(static::FILE_DELETE_FLAG)) {
            $this->destroy();
            return;
        }

        $this->name = $this->getStoreName($file);

        return $this->uploadAndDeleteOriginal($file);
    }


    /**
     * Upload file and delete original file.
     *
     * @param UploadedFile $file
     *
     * @return mixed
     */
    protected function uploadAndDeleteOriginal(?UploadedFile $file)
    {
        if (is_null($file)) {
            return null;
        }

        $this->renameIfExists($file);

        $path = null;

        if (!is_null($this->storagePermission)) {
            $path = $this->storage->putFileAs($this->getDirectory(), $file, $this->name, $this->storagePermission);
        } else {
            $path = $this->storage->putFileAs($this->getDirectory(), $file, $this->name);
        }

        $this->destroy();

        return $path;
    }

    /**
     * Preview html for file-upload plugin.
     *
     * @return string
     */
    protected function preview()
    {
        return $this->objectUrl($this->value);
    }

    /**
     * set fileIndex.
     *
     * @param \Closure $fileIndex
     *
     * @return $this
     */
    public function fileIndex($fileIndex)
    {
        $this->fileIndex = $fileIndex;

        return $this;
    }

    /**
     * set caption.
     *
     * @param \Closure $caption
     *
     * @return $this
     */
    public function caption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Initialize the index.
     *
     * @param mixed $file
     * @return int|mixed
     */
    protected function initialFileIndex($file)
    {
        if ($this->fileIndex instanceof \Closure) {
            return $this->fileIndex->call($this, 0, $file);
        }
        return 0;
    }

    /**
     * Initialize the caption.
     *
     * @param string $caption
     * @param mixed $key
     *
     * @return string
     */
    protected function initialCaption($caption, $key)
    {
        if ($this->caption instanceof Closure) {
            return $this->caption->call($this, $caption, $key);
        }
        return basename($caption);
    }

    /**
     * @return array<int, array<mixed, mixed>>
     */
    protected function initialPreviewConfig()
    {
        $key = $this->initialFileIndex($this->value);
        $config = ['caption' => $this->initialCaption($this->value, $key), 'key' => $key];

        $config = array_merge($config, $this->guessPreviewType($this->value));

        return [$config];
    }

    /**
     * @param string $options
     * @return  void
     */
    protected function setupScripts($options)
    {
        $this->script = <<<EOT
$("{$this->getElementClassSelector()}").each(function(index, element){
    var options = {$options};
    options['fileActionSettings'] = {
        showRemove: true,
        removeIcon: '<i class="fas fa-trash-alt"></i>',
        showUpload: true,
        uploadIcon: '<i class="fas fa-upload"></i>',
        showDownload: true,
        downloadIcon: '<i class="fas fa-download"></i>',
        showZoom: false,
    };
    options['browseIcon'] = '<i class="fas fa-folder-open"></i>';
    options['allowedFileExtensions'] = ["jpg", "png", "gif"];
    options['initialPreviewAsData'] = true;
    options['initialPreview'] = $(element).data('initial-preview');
    $(element).fileinput(options);
});
EOT;

        if ($this->fileActionSettings['showRemove']) {
            $text = [
                'title' => trans('admin.delete_confirm'),
                'confirm' => trans('admin.confirm'),
                'cancel' => trans('admin.cancel'),
            ];

            $this->script .= <<<EOT
$("{$this->getElementClassSelector()}").on('filebeforedelete', function() {
    return new Promise(function(resolve, reject) {
        var remove = resolve;
        swal({
            title: "{$text['title']}",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "{$text['confirm']}",
            showLoaderOnConfirm: true,
            cancelButtonText: "{$text['cancel']}",
            preConfirm: function() {
                return new Promise(function(resolve) {
                    resolve(remove());
                });
            }
        });
    });
});
EOT;

            if (isset($this->options['deletedEvent'])) {
                $deletedEvent = $this->options['deletedEvent'];
                $this->script .= <<<EOT
$("{$this->getElementClassSelector()}").on('filedeleted', function(event, key, jqXHR, data) {
    {$deletedEvent};
});
EOT;
            }
        }

    }

    /**
     * Render file upload field.
     * @return string
     */
    public function render()
    {
        $this->options(['overwriteInitial' => true, 'msgPlaceholder' => trans('admin.choose_file')]);

        $this->setupDefaultOptions();

        if ($this->callbackValue instanceof Closure) {
            $this->value = $this->callbackValue->call($this, $this->value);
        }

        if (!empty($this->value)) {
            $this->setupPreviewOptions();

            $this->attribute('data-initial-preview', $this->preview());
            $this->attribute('data-initial-caption', Arr::get($this->options, 'initialPreviewConfig.0.caption'));

            $previewType = $this->guessPreviewType($this->value);
            $this->attribute('data-initial-type', Arr::get($previewType, 'type'));
            $this->attribute('data-initial-download-url', Arr::get($previewType, 'downloadUrl'));
            /*
             * If has original value, means the form is in edit mode,
             * then remove required rule from rules.
             */
            unset($this->attributes['required']);
        }

        $options = json_encode_options($this->options);

        $this->setupScripts($options);

        return parent::render();
    }
}
