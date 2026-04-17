<?php

namespace App\Http\Controllers;

class SiteSettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }
}
