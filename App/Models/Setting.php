<?php

namespace App\Models;


class Setting extends \Core\Model
{
    public static $table = 'settings';

    public static function addOrUpdate($name, $value)
    {
        $currency_symbol=Setting::first('name', $name);
        if($currency_symbol){
            $price=$currency_symbol;
            $price->name=$name;
            $price->value=$value;
            $price->update();
        }else{
            $price=new Setting();
            $price->name=$name;
            $price->value=$value;
            $price->save();
        }
    }

    public static function getPictureDimensions()
    {
        $picture_dimensions = \App\Models\Setting::haveRow('name', 'picture-dimensions', 'value');
        if($picture_dimensions == null) {
            $picture_dimensions = '|';
        }
        return explode('|', $picture_dimensions);
    }

    public static function haveRow($name, $value, $column)
    {
        $row=\App\Models\Setting::first($name, $value);
        return $row ? $row->$column : null;

    }


}
