<?php
/**
 * Created by PhpStorm.
 * User: blade
 * Date: 9.10.2018 г.
 * Time: 17:48
 */

namespace App\Models;

use DateTime;


class Product extends \Core\Model
{
    protected static $table = 'products';

    public static function isNewProduct($product)
    {
        $new_product=Setting::haveRow('name', 'new-product', 'value');
        if($new_product) {
            $datetime1 = new DateTime($product->updated_at);
            $datetime2 = new DateTime(date("Y-m-d H:i:s"));
            $interval = $datetime1->diff($datetime2);
            return $interval->format('%a') < $new_product;
        }
    }
}