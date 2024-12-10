<?php
namespace OpenAdmin\Admin\Validator;

use Illuminate\Contracts\Validation\Rule;

/**
 * Check number.
 */
class DigitMaxRule implements Rule
{
    /**
     * @var int
     */
    protected $max;

    /**
     * @param int $max
     */
    public function __construct($max)
    {
        $this->max = $max;
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

        if($value > $this->max){
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
        return trans('validation.max.numeric', ['max' => $this->max]);
    }
}
