<?php

namespace OpenAdmin\Admin\Controllers;

use OpenAdmin\Admin\Widgets\Form;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HandleController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     * @throws Exception
     */
    public function handleForm(Request $request)
    {
        if (!$request->has('_form_')) {
            throw new Exception('Invalid form request.');
        }

        $formClass = $request->get('_form_');

        if (!class_exists($formClass)) {
            throw new Exception("Form [{$formClass}] not exists.");
        }

        /** @var Form $form */
        $form = app($formClass);

        if (!method_exists($form, 'handle')) {
            throw new Exception("Form method {$formClass}::handle() not exists.");
        }

        if ($errors = $form->validate($request)) {
            return back()->withInput()->withErrors($errors);
        }

        return $form->sanitize()->handle($request);
    }
}
