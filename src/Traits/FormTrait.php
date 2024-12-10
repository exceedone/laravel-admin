<?php

namespace OpenAdmin\Admin\Traits;

use Illuminate\Support\Arr;

trait FormTrait
{
    /**
     * unique class name for class selector
     * 
     * @var string
     */
    protected $uniqueName;

    /**
     * If the form horizontal layout.
     *
     * @var bool
     */
    protected $horizontal = true;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [];

    /**
     * Set unique class name for class selector
     * @param string $uniqueName
     *
     * @return  $this
     */ 
    public function setUniqueName($uniqueName)
    {
        $this->uniqueName = $uniqueName;
        return $this;
    }

    /**
     * Get unique class name for class selector
     *
     * @return  string
     */ 
    public function getUniqueName()
    {
        if(!$this->uniqueName){
            $this->uniqueName = 'form-' . mb_substr(md5(uniqid()), 0, 32);
        }
        return $this->uniqueName;
    }

    /**
     * @return bool
     */
    public function getHorizontal()
    {
        return $this->horizontal;
    }

    /**
     * @return $this
     */
    public function setHorizontal(bool $horizontal)
    {
        $this->horizontal = $horizontal;
        
        return $this; 
    }

    /**
     * @return $this
     */
    public function disableHorizontal()
    {
        $this->horizontal = false;

        return $this;
    }

    /**
     * Add form attributes.
     *
     * @param string|array<mixed> $attr
     * @param string|int       $value
     *
     * @return $this
     */
    public function attribute($attr, $value = '')
    {
        if (is_array($attr)) {
            foreach ($attr as $key => $value) {
                if($key == 'class'){
                    $this->setClass($value);
                }
                else{
                    $this->attribute($key, $value);
                }
            }
        } else {
            if($attr == 'class'){
                $this->setClass($value);
            }
            else{
                $this->attributes[$attr] = $value;
            }
        }

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(){
        return $this->attributes;
    }

    /**
     * set class
     *
     * @param string|array<mixed> $value
     * @return $this
     */
    public function setClass($value)
    {
        $result = explode_ex(' ', Arr::get($this->attributes, 'class'));

        if(is_string($value)){
            $value = explode_ex(' ', $value);
        }
        foreach($value as $v){
            if(empty($v)){
                continue;
            }
            $result[] = $v;
        }

        $result = implode(' ', array_unique($result));
        $this->attributes['class'] = $result;

        return $this;
    }
}
