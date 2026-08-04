<?php
namespace Encore\Admin\Validator;

use Illuminate\Contracts\Validation\Rule;

/**
 * Has option select field.
 */
class HasOptionRule implements Rule
{
    /**
     * @var mixed
     */
    protected $field;

    /**
     * @param mixed $field
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
    * Check Validation
    *
    * @param  string  $attribute
    * @param  array<mixed>|string|null  $value
    * @return bool
    */
    public function passes($attribute, $value)
    {
        // @phpstan-ignore-next-line $object_or_class is always object|string at runtime
        if(!method_exists($this->field, 'getOptions')){
            return true;
        }

        if(is_array($value)){
            $value = array_filter($value);
        }

        if(is_null($value) || $value == ''){
            return true;
        }

        /** @phpstan-ignore-next-line Cannot call method getOptions() on class-string|object. */
        $options = $this->field->getOptions($value);

        if(!is_array($value)){
            $value = (array)$value;
        }

        foreach($value as $val){
            // @phpstan-ignore-next-line $value is always bool|float|int|resource|string|null at runtime
            if(!array_key_exists(strval($val), $options)){
                return false;
            }
        }

        return true;
    }
    
    /**
     * get validation error message
     *
     * @return string
     */
    public function message()
    {
        return trans('admin.validation.not_in_option');
    }
}
