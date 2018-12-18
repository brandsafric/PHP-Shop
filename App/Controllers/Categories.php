<?php
/**
 * Created by PhpStorm.
 * User: blade
 * Date: 9.10.2018 г.
 * Time: 15:50
 */

namespace App\Controllers;


use App\Models\Category;

class Categories
{
    public static function findParent($arr, $parent)
    {
        foreach ($arr as $key=>$value){
            if($value->id === $parent){
                return $key;
            }
        }
    }

    public function index()
    {
        return view('admin.list-categories', ['categories' => Category::all()]);
    }

    public function create()
    {
        $categories=Category::all();
        $cat=[];
        foreach ($categories as $key=>$category){
            $cat[]=$category;
            if($category->parent_id != null){
                $cat[$key]->name=$categories[self::findParent($categories, $category->parent_id)]->name.'->'.$category->name;
            }
        }
        return view('admin.add-category', ['cats' => $cat]);
    }

    public function update($id)
    {
        $name = filter_input(INPUT_POST,'name',FILTER_SANITIZE_STRING);
        $alias = filter_input(INPUT_POST,'alias',FILTER_SANITIZE_STRING);
        $parent = filter_input(INPUT_POST,'parent');
        if($name==''){
            add_error('Field for category name can not be empty!');
        }
        if($alias==''){
            add_error('Field for alias can not be empty!');
        }
        $temp_cat=Category::where('alias', $alias);
        if($temp_cat && $temp_cat[0]->id != $id){
            add_error('There is already have category with alias "' . $alias . '"');
        }

        if(!haveErrors()){
            $category = Category::find($id);
            $category->name=ucwords($name);
            $category->alias=mb_strtolower($alias);
            $category->parent_id = null;
            $category->update();
            add_message('Successfully update category');
        }
        redirect_back();
    }

    public function destroy($id)
    {
        $category=Category::find($id);
        $category->delete();
        add_message('Category was successfully deleted');
        redirect_back();
    }

    public function store()
    {
        $name = filter_input(INPUT_POST,'name',FILTER_SANITIZE_STRING);
        $alias = filter_input(INPUT_POST,'alias',FILTER_SANITIZE_STRING);
        $parent = filter_input(INPUT_POST,'parent');
        if($name==''){
            add_error('Field for category name can not be empty!');
        }
        if($alias==''){
            add_error('Field for alias can not be empty!');
        }elseif(Category::where('alias',$alias)){
            add_error('There is already have category with alias "' . $alias . '"');
        }

        if(!haveErrors()){
            $category = new Category();
            $category->name=ucwords($name);
            $category->alias=strtolower($alias);
            $category->parent_id=$parent!='' ? $parent : NULL;
            $category->save();
            add_message('Successfully added category');
        }
        redirect_back();
    }
}