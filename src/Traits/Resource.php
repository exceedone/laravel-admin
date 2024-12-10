<?php

namespace OpenAdmin\Admin\Traits;

trait Resource
{
    /**
     * get resource to grid.
     *
     * @param int $slice
     *
     * @return string
     */
    protected function getResource($slice)
    {
        // create uri
        $segments = [];

        // set url
        foreach (explode('/', trim(app('request')->getPathInfo(), '/')) as $value) {
            $segments[] = $value;
        }

        if ($slice != 0) {
            $segments = array_slice($segments, 0, $slice);
        }

        return '/'.implode('/', $segments);
    }
}
