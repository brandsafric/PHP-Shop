<?php
/**
 * Created by PhpStorm.
 * User: blade
 * Date: 20.10.2018 г.
 * Time: 10:52
 */

namespace App\Models;


use Core\Data;

class ProductOrder extends \Core\Model
{
    public static $table='productorders';

    public static function getWhereClause()
    {
        return Data::userIsLoggedIn() ? "user_id=" . $_SESSION['user'] : "session_id='" . session_id() . "'";
    }

    public static function getUnfinishedOrdersForUser()
    {
        $where_clause=self::getWhereClause();
//        my_var_dump($where_clause);

        $orders=ProductOrder::query("
                    SELECT po.id, po.product_id, po.qty, po.user_id, po.variation, p.title, 
                    p.picture_id, p.price, p.variation_name, p.promo_price, t.path  
                    FROM productorders as po
                    INNER JOIN products as p
                    ON po.product_id=p.id 
                    LEFT JOIN thumbnails as t
                    ON p.picture_id=t.picture_id
                    WHERE $where_clause
                    AND order_id IS NULL 
                    GROUP BY po.id");
        return $orders;
    }

    public static function haveUnfinishedOrders()
    {
        $where_clause=self::getWhereClause();
        $orders=ProductOrder::query("SELECT id FROM productorders WHERE $where_clause AND order_id IS NULL LIMIT 1");
        return count($orders) > 0;
    }

    public static function getTotalPrice($orders)
    {
        $total=0;
        foreach ($orders as $order){
            $total += ($order->promo_price!=null ? $order->promo_price : $order->price) * $order->qty;
        }
        return $total;
    }

}