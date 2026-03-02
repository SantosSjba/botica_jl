<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

abstract class Controller
{
    /**
     * Si la petición es AJAX/JSON, devuelve JSON con success, message y redirect.
     * Si no, redirige con flash.
     */
    protected function successRedirect(string $message, string $redirectUrl): JsonResponse|RedirectResponse
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => $message, 'redirect' => $redirectUrl]);
        }
        return redirect()->to($redirectUrl)->with('success', $message);
    }

    /**
     * Si la petición es AJAX/JSON, devuelve JSON con success false y message.
     * Si no, redirige con flash error.
     */
    protected function errorRedirect(string $message, string $redirectUrl): JsonResponse|RedirectResponse
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => false, 'message' => $message]);
        }
        return redirect()->to($redirectUrl)->with('error', $message);
    }
}
