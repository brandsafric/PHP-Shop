<?php
/**
 * Created by PhpStorm.
 * User: blade
 * Date: 9.10.2018 г.
 * Time: 15:50
 */

namespace App\Controllers;


use App\Models\Category;
use App\Models\Setting;

class Thumbnails
{

    public function index()
    {
        return view('admin.list-categories', ['categories' => Category::all()]);
    }

    public function create()
    {

        return view('admin.add-category');
    }

    public function update($id)
    {

    }

    public function destroy($id)
    {

    }

}