<?php
/**
 * Created by PhpStorm.
 * User: blade
 * Date: 9.10.2018 г.
 * Time: 17:48
 */

namespace App\Models;


class Category extends \Core\Model
{
    protected static $table = 'categories';

    public function isParent()
    {
        return $this->parent_id == null;
    }

    public static function childCategories1(&$arr, $id)
    {
        $cat=Category::where('parent_id', $id);
        if($cat!==null) {
            foreach ($cat as $c) {
                $arr[] = $c->id;
                self::childCategories1($arr, $c->id);
            }
        }
    }

    public static function childCategories($alias)
    {
        $result=[];
        $result[]=Category::first('alias', $alias)->id;
        self::childCategories1($result, $result[0]);
//        my_var_dump($result);
        return $result;
    }
}