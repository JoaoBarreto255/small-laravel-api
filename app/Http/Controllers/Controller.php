<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class Controller
{
    public function index(): Response
    {
        return new Response(status: 204);
    }
}
