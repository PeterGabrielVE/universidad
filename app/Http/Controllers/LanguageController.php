<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        // Add logic to handle language switching
        $request->validate(['language' => 'required|string']);
        session(['locale' => $request->language]);
        return redirect()->back();
    }
}
