<?php
namespace OpenAdmin\Admin\Validator;

use Illuminate\Contracts\Validation\Rule;

/**
 * Check between number.
 */
class DigitMinRule implements Rule
{
/**
     * @var int
     */
    protected $min;

    /**
     * @param int $min
     */
    public function __construct($min)
    {
        $this->min = $min;
    }

    /**
    * Check Validation
    *
    * @param  string  $attribute
    * @param  mixed  $value
    * @return bool
    */
    public function passes($attribute, $value)
    {
        if(is_null($value)){
            return true;
        }
        if(!is_numeric($value)){
            return false;
        }

        $value = intval($value);

        if($value < $this->min){
            return false;
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
        return trans('validation.min.numeric', ['min' => $this->min]);
    }
}
