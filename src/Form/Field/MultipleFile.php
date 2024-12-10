<?php

namespace OpenAdmin\Admin\Form\Field;

use OpenAdmin\Admin\Form\Field;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MultipleFile extends Field
{
    use UploadField;

    /**
     * Css.
     *
     * @var array<string>
     */
    protected static $css = [
        '/vendor/open-admin/bootstrap-fileinput/css/fileinput.min.css?v=4.5.2',
    ];

    /**
     * Js.
     *
     * @var array<string>
     */
    protected static $js = [
        '/vendor/open-admin/bootstrap-fileinput/js/plugins/canvas-to-blob.min.js',
        '/vendor/open-admin/bootstrap-fileinput/js/fileinput.min.js?v=4.5.2',
        '/vendor/open-admin/bootstrap-fileinput/js/plugins/sortable.min.js?v=4.5.2',
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
     *
     * @return bool|\Illuminate\Contracts\Validation\Validator|\Illuminate\Contracts\Validation\Factory
     */
    public function getValidator(array $input)
    {
        if (request()->has(static::FILE_DELETE_FLAG)) {
            return false;
        }

        if (request()->has(static::FILE_SORT_FLAG)) {
            return false;
        }

        if ($this->validator) {
            return $this->validator->call($this, $input);
        }

        $attributes = [];

        if (!$fieldRules = $this->getRules()) {
            return false;
        }

        $attributes[$this->column] = $this->label;
        $fileNames = Arr::get($input, $this->column);
        list($rules, $input) = $this->hydrateFiles($fileNames ? (is_array($fileNames) ? $fileNames : $fileNames->toArray()) : []);

        return \validator($input, $rules, $this->getValidationMessages(), $attributes);
    }

    /**
     * Hydrate the files array.
     *
     * @param array<mixed> $value
     *
     * @return array<mixed>
     */
    protected function hydrateFiles(array $value)
    {
        if (empty($value)) {
            return [[$this->column => $this->getRules()], []];
        }

        $rules = $input = [];

        foreach ($value as $key => $file) {
            $rules[$this->column.$key] = $this->getRules();
            $input[$this->column.$key] = $file;
        }

        return [$rules, $input];
    }

    /**
     * Sort files.
     *
     * @param string $order
     *
     * @return array<mixed>
     */
    protected function sortFiles($order)
    {
        $order = explode(',', $order);

        $new = [];
        $original = $this->original();

        foreach ($order as $item) {
            $new[] = Arr::get($original, $item);
        }

        return $new;
    }

    /**
     * Prepare for saving.
     *
     * @param UploadedFile|array<mixed> $files
     *
     * @return mixed|string
     */
    public function prepare($files)
    {
        // If has $files is array, items is all string and has TMP_FILE_PREFIX, get $file
        if (is_array($files) && $this->getTmp) {
            if (!collect($files)->contains(function ($file) {
                // If has $file is string, and has TMP_FILE_PREFIX, get $file
                return !is_string($file) || strpos($file, File::TMP_FILE_PREFIX) !== 0;
            })) {
                $files = call_user_func($this->getTmp, $files);
            }
        }

        if (request()->has(static::FILE_DELETE_FLAG)) {
            return $this->destroy(request(static::FILE_DELETE_FLAG));
        }

        if (is_string($files) && request()->has(static::FILE_SORT_FLAG)) {
            return $this->sortFiles($files);
        }

        if(is_string($files)){
            $files = [$files];
        }
        $targets = array_map([$this, 'prepareForeach'], $files);
        
        // get original
        $original = $this->original();
        if(is_string($original)){
            $original = [$original];
        }

        return array_merge($original, $targets);
    }

    /**
     * @return array<mixed>|mixed
     */
    public function original()
    {
        if (empty($this->original)) {
            return [];
        }

        return $this->original;
    }

    /**
     * Prepare for each file.
     *
     * @param UploadedFile $file
     *
     * @return mixed|string
     */
    protected function prepareForeach(UploadedFile $file = null)
    {
        $this->name = $this->getStoreName($file);

        return tap($this->upload($file), function () {
            $this->name = null;
        });
    }

    /**
     * Preview html for file-upload plugin.
     *
     * @return array<mixed>
     */
    protected function preview()
    {
        $files = $this->value ?: [];
        if(is_string($files)){
            $files = [$files];
        }
        return array_values(array_map([$this, 'objectUrl'], $files));
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
     * @param mixed $index
     * @param mixed $file
     * @return mixed
     */
    protected function initialFileIndex($index, $file)
    {
        if($this->fileIndex instanceof \Closure){
            return $this->fileIndex->call($this, $index, $file);
        }
        return $index;
    }

    /**
     * Initialize the caption.
     *
     * @param string $caption
     * @param string $key
     *
     * @return string
     */
    protected function initialCaption($caption, $key)
    {
        if($this->caption instanceof \Closure){
            return $this->caption->call($this, $caption, $key);
        }
        return basename($caption);
    }

    /**
     * @return array<mixed>
     */
    protected function initialPreviewConfig()
    {
        $files = $this->value ?: [];
        
        if(is_string($files)){
            $files = [$files];
        }

        $config = [];

        foreach ($files as $index => $file) {
            $key = $this->initialFileIndex($index, $file);
            $preview = array_merge([
                'caption' => $this->initialCaption($file, $key),
                'key'     => $key,
            ], $this->guessPreviewType($file));

            $config[] = $preview;
        }

        return $config;
    }

    /**
     * Allow to sort files.
     *
     * @return $this
     */
    public function sortable()
    {
        $this->fileActionSettings['showDrag'] = true;

        return $this;
    }

    /**
     * @param string $options
     *
     * @return void
     */
    protected function setupScripts($options)
    {
        $this->script = <<<EOT
$("{$this->getElementClassSelector()}").fileinput({$options});
EOT;

        if ($this->fileActionSettings['showRemove']) {
            $text = [
                'title'   => trans('admin.delete_confirm'),
                'confirm' => trans('admin.confirm'),
                'cancel'  => trans('admin.cancel'),
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
            if(isset($this->options['deletedEvent'])){
                $deletedEvent = $this->options['deletedEvent'];
                $this->script .= <<<EOT
                $("{$this->getElementClassSelector()}").on('filedeleted', function(event, key, jqXHR, data) {
                    {$deletedEvent};
                });
EOT;
            }
        }

        if ($this->fileActionSettings['showDrag']) {
            $this->addVariables([
                'sortable'  => true,
                'sort_flag' => static::FILE_SORT_FLAG,
            ]);

            $this->script .= <<<EOT
$("{$this->getElementClassSelector()}").on('filesorted', function(event, params) {
    
    var order = [];
    
    params.stack.forEach(function (item) {
        order.push(item.key);
    });
    
    $("{$this->getElementClassSelector()}_sort").val(order);
});
EOT;
        }
    }

    /**
     * Render file upload field.
     *
     * @return string
     */
    public function render()
    {
        $this->attribute('multiple', true);

        $this->setupDefaultOptions();

        if (!empty($this->value)) {
            $this->options(['initialPreview' => $this->preview()]);
            $this->setupPreviewOptions();
            /*
             * If has original value, means the form is in edit mode,
             * then remove required rule from rules.
             */
            unset($this->attributes['required']);
        }

        $options = json_encode($this->options);

        $this->setupScripts($options);

        return parent::render();
    }

    /**
     * Destroy original files.
     *
     * @param string $key
     *
     * @return array<mixed>
     */
    public function destroy($key)
    {
        $files = $this->original ?: [];

        $file = Arr::get($files, $key);

        if (!$this->retainable && $this->storage->exists($file)) {
            $this->storage->delete($file);
        }

        unset($files[$key]);

        return $files;
    }
}
