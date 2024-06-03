<?php

namespace Encore\Admin\Grid;

/**
 * grid link.
 */
class Linker
{
    /**
     * Link icon
     *
     * @var mixed
     */
    protected $icon;

    /**
     * tooltip text
     *
     * @var mixed
     */
    protected $tooltip;

    /**
     * link url
     *
     * @var mixed
     */
    protected $url;

    /**
     * script. if true, as script
     *
     * @var mixed
     */
    protected $script = false;

    /**
     * link attributes
     *
     * @var mixed
     */
    protected $linkattributes = [];

    /**
     * icon attributes
     *
     * @var mixed
     */
    protected $iconattributes = [];

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
    }

    public static function make(){
        return new Linker();
    }

    public function icon($icon =  null){
        if (!func_num_args()) {
            return $this->icon;
        }

        $this->icon = $icon;
        return $this;
    }

    public function tooltip($tooltip =  null){
        if (!func_num_args()) {
            return $this->tooltip;
        }

        $this->tooltip = $tooltip;
        return $this;
    }

    public function url($url =  null){
        if (!func_num_args()) {
            return $this->url;
        }

        $this->url = $url;
        return $this;
    }

    public function script($script =  null){
        if (!func_num_args()) {
            return $this->script;
        }

        $this->script = $script;
        return $this;
    }

    public function linkattributes($linkattributes = null){
        if (!func_num_args()) {
            return $this->linkattributes;
        }

        foreach($linkattributes as $key => $attribute){
            $this->linkattributes[$key] = $attribute;
        }
        return $this;
    }
    
    public function iconattributes($iconattributes = null){
        if (!func_num_args()) {
            return $this->iconattributes;
        }

        foreach($iconattributes as $key => $attribute){
            $this->iconattributes[$key] = $attribute;
        }
        return $this;
    }

    /**
     * render html
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        if(!isset($this->url)){
            $this->url = '';
        }
        
        // add tooltip
        if(isset($this->tooltip)){
            $this->linkattributes['data-toggle'] = 'tooltip';
            $this->linkattributes['title'] = $this->tooltip;
        }

        $linkattribute = $this->getParams($this->linkattributes);
        
        $iconattribute = $this->getParams($this->iconattributes);
        
        if(!isset($this->url)){
            $this->url = '';
        }

        return view('admin::grid.linker', [
            'script' => $this->script,
            'url' => $this->url,
            'icon' => $this->icon,
            'linkattribute' => $linkattribute,
            'iconattribute' => $iconattribute,
        ]);
    }

    public function __toString(){
        return $this->render()->render();
    }

    protected function getParams($array){
        return implode(" ", collect($array)->map(function($attribute, $key){
            $attribute = htmlspecialchars($attribute, ENT_QUOTES|ENT_HTML5);
            return "{$key}='{$attribute}'";
        })->toArray());
    }
}
