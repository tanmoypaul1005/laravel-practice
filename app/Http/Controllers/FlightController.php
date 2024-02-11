<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function getCategories()
    {
        try {
            $categories = Category::get();
            return $categories;
        } catch (Exception $e) {
            return "fail";
        }
    }
}
