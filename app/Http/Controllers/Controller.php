<?php

namespace App\Http\Controllers;
 
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Furniture API Documentation",
    version: "1.0.0",
    description: "API documentation for the Furniture E-commerce Application",
    contact: new OA\Contact(email: "admin@example.com")
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "Current API Server"
)]
abstract class Controller
{
    //
}
