<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    // Trait ini menyediakan helper $this->authorize(...) yang dipakai
    // di seluruh controller untuk memanggil Policy (ItemReportPolicy, ClaimPolicy).
    use AuthorizesRequests, ValidatesRequests;
}
