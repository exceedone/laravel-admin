<?php

namespace Encore\Admin\Controllers;

trait HasResourceActions
{
    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        // @phpstan-ignore-next-line The value is always an object exposing update() at runtime (and 1 more mixed-type assumption on this line)
        return $this->form()->update($id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store()
    {
        // @phpstan-ignore-next-line The value is always an object exposing store() at runtime
        return $this->form()->store();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // @phpstan-ignore-next-line The value is always an object exposing destroy() at runtime (and 1 more mixed-type assumption on this line)
        return $this->form()->destroy($id);
    }
}
