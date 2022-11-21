<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
        $returnMessage = [
            'service_name' => 'PHP Service App created with Lumen By Rey Muhamad Rifqi',
            'status' => 'Running'
        ];

        return response()->json($returnMessage);
    }

    //
}
