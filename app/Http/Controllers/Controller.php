<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Swagger API Documentation",
     *      description="Laravel"
     * )
     *
     * @OA\Server(
     *      url="http://127.0.0.1:8000/",
     *      description="Demo API Server"
     * )

     *
     *
     */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
