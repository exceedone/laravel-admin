<?php
namespace Encore\Admin\Validator;

use Illuminate\Contracts\Validation\Rule;

/**
 * Has option select field.
 */
class HasOptionRule implements Rule
{
    protected $field;

    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
    * Check Validation
    *
    * @param  string  $attribute
    * @param  array|string|null  $value
    * @return bool
    */
    public function passes($attribute, $value)
    {
        if(!method_exists($this->field, 'getOptions')){
            return true;
        }

        if(is_array($value)){
            $value = array_filter($value);
        }

        if(is_null($value) || $value == ''){
            return true;
        }

        $options = $this->field->getOptions($value);

        if(!is_array($value)){
            $value = (array)$value;
        }

        foreach($value as $val){
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
