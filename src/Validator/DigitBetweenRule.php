<?php
namespace OpenAdmin\Admin\Validator;

use Illuminate\Contracts\Validation\Rule;

/**
 * Check between number.
 */
class DigitBetweenRule implements Rule
{
    /**
     * @var int
     */
    protected $min;
    /**
     * @var int
     */
    protected $max;

    /**
     * @param int $min
     * @param int $max
     */
    public function __construct($min, $max)
    {
        $this->min = $min;
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

        if($value < $this->min){
            return false;
        }
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
        return trans('validation.between.numeric', ['min' => $this->min, 'max' => $this->max]);
    }
}
