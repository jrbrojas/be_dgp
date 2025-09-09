<?php

namespace App\Http\Controllers;

use App\Models\UbigeoPeruDepartment;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        return UbigeoPeruDepartment::all();
    }
}
