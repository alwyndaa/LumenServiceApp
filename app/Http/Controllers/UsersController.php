<?php

namespace App\Http\Controllers;

class UsersController extends Controller
{
    private $users = [
        1 => ['id' => 1, 'name' => 'Sumatrana', 'email' => 'sumatrana@gmail.com', 'address' => 'Padang', 'gender' => 'Laki-laki'],
        2 => ['id' => 2, 'name' => 'Jawarianto', 'email' => 'jawarianto@gmail.com', 'address' => 'Cimahi', 'gender' => 'Laki-laki'],
        3 => ['id' => 3, 'name' => 'Kalimantanio', 'email' => 'kalimantanio@gmail.com', 'address' => 'Samarinda', 'gender' => 'Laki-laki'],
        4 => ['id' => 4, 'name' => 'Sulawesiani', 'email' => 'sulawesiani@gmail.com', 'address' => 'Makasar', 'gender' => 'Perempuan'],
        5 => ['id' => 5, 'name' => 'Papuani', 'email' => 'papuani@gmail.com', 'address' => 'Jayapura', 'gender' => 'Perempuan'],
    ];

    // GET /users
    public function index()
    {
        return response()->json(array_values($this->users));
    }

    // GET /users/{userId}
    public function show($userId)
    {
        if (!isset($this->users[$userId])) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }
        return response()->json($this->users[$userId]);
    }
}