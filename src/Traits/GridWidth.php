<?php

namespace OpenAdmin\Admin\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

trait GridWidth
{
    /**
     * grid system prefix width.
     *
     * @var array<string, int>|int
     */
    protected $width = [];

    /**
     * set grid width
     *
     * @param int|array<mixed>|null $width
     *
     * @return $this
     */
    public function setWidth($width = 12)
    {
        ///// set width.
        // if null, or $this->width is empty array, set as "sm" => "12"
        if (is_null($width) || (is_array($width) && count($width) === 0)) {
            $this->width['sm'] = 12;
        }
        // $this->width is number(old version), set as "sm" => $width
        elseif (is_numeric($width)) {
            $this->width['sm'] = $width;
        } else {
            $this->width = $width;
        }

        return $this;
    }

    /**
     * ^getGridWidthClass
     * 
     * @return string grid system class as col-XX-12 
     */
    protected function getGridWidthClass()
    {
        return collect($this->width)->map(function ($value, $key) {
            return "col-$key-$value";
        })->implode(' ');
    }

}
