<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
        $users = [
            ['id' => 1, 'name' => 'Sumatrana', 'email' => 'sumatrana@gmail.com', 'address' => 'Padang', 'gender' => 'Laki - laki'],
            ['id' => 2, 'name' => 'Jawarinto', 'email' => 'jawarinto@gmail.com', 'address' => 'Cimahi', 'gender' => 'Laki - laki'],
            ['id' => 3, 'name' => 'Kalimantanio', 'email' => 'kalimantanio@gmail.com', 'address' => 'Samarinda', 'gender' => 'Laki - laki'],
            ['id' => 4, 'name' => 'Sulawesiani', 'email' => 'sulawesiani@gmail.com', 'address' => 'Makasar', 'gender' => 'Perempuan'],
            ['id' => 5, 'name' => 'Papuani', 'email' => 'papuani@gmail.com', 'address' => 'Jayapura', 'gender' => 'Perempuan']
        ];

        return response()->json($users);
    }

    public function show($id)
    {
        $users = [
            '1' => ['id' => 1, 'name' => 'Sumatrana', 'email' => 'sumatrana@gmail.com', 'address' => 'Padang', 'gender' => 'Laki - laki'],
            '2' => ['id' => 2, 'name' => 'Jawarinto', 'email' => 'jawarinto@gmail.com', 'address' => 'Cimahi', 'gender' => 'Laki - laki'],
            '3' => ['id' => 3, 'name' => 'Kalimantanio', 'email' => 'kalimantanio@gmail.com', 'address' => 'Samarinda', 'gender' => 'Laki - laki'],
            '4' => ['id' => 4, 'name' => 'Sulawesiani', 'email' => 'sulawesiani@gmail.com', 'address' => 'Makasar', 'gender' => 'Perempuan'],
            '5' => ['id' => 5, 'name' => 'Papuani', 'email' => 'papuani@gmail.com', 'address' => 'Jayapura', 'gender' => 'Perempuan']
        ];

        $user = $users[$id];

        return response()->json($user);
    }
}
