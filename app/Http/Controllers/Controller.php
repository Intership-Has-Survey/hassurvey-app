<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //

    public function setCompanyContext($uuid)
    {
        // Optional: validasi UUID cocok dengan company milik user
        if (!auth()->user()->companies->contains('id', $uuid)) {
            abort(403, 'Unauthorized company');
        }

        session(['company_id' => $uuid]);

        // return redirect('/admin/dashboard');
    }
}
