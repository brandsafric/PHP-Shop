<?php

namespace App\Models;


class Thumbnail extends \Core\Model
{
    public static $table = 'thumbnails';

    public static function getThumbnail($picture_id)
    {
        if($picture_id==null){
            return '/placeholder.jpg';
        }
        return Thumbnail::where('picture_id', $picture_id)[0]->path;
    }
}
