<?php

namespace App\Controllers;

use App\Core\View;

class IndexController
{
    public function index(): string
    {
        return View::render('home', [
            'title' => 'MVC Framework',
            'message' => 'Hello from controller'
        ]);
    }
}