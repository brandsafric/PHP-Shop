<?php
/**
 * Created by PhpStorm.
 * User: blade
 * Date: 20.10.2018 г.
 * Time: 10:52
 */

namespace App\Models;


class Order extends \Core\Model
{
    public static $table='orders';

    public static function getStatusBadgeColor($status)
    {
        switch ($status){
            case 'Processing':return 'bg-green';break;
            case 'Completed':return 'bg-blue';break;
            case 'On hold':return 'bg-yellow';break;
            case 'Failed':return 'bg-red';break;
            case 'Refunded':return 'bg-maroon';break;
//            case 'Cancelled':return 'bg-gray';break;
        }
    }

}