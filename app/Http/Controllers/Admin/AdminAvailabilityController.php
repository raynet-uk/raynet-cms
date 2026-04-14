<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

class AdminAvailabilityController extends Controller
{
    public function index()
    {
        return view('admin.availability.index');
    }
}