<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Muestra la página de perfil del usuario autenticado.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        return view('pages.profile', [
            'title' => 'Perfil',
            'user'  => $user,
        ]);
    }
}
