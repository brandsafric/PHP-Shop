<?php

namespace Core;

use App\Config;
use App\Models\Setting;
use App\Models\Thumbnail;

class Image
{

    private $filename;
    private $type;
    private $thumbnails = [];

    public function __get($name) {
        return $this->$name;
    }


    public function __construct($src = null)
    {
        $this->filename = $src;
        if($src!=null){
            $this->type=exif_imagetype($src);
        }
    }

    public function crop($new_width, $new_height)
    {
        if($this->filename!==null) {
            $this->type=exif_imagetype($this->filename);
            $img = null;
            if ($this->type === IMAGETYPE_PNG) {
                $img = imagecreatefrompng($this->filename);
            } else {
                $img = imagecreatefromjpeg($this->filename);
            }
            $blank_image = imagecreatetruecolor($new_width, $new_height);
            $aspect_ratio = min(imagesx($img) / $new_width, imagesy($img) / $new_height);
            $new_img = imagescale($img, imagesx($img) / $aspect_ratio, imagesy($img) / $aspect_ratio);
            imagecopy($blank_image, $new_img, 0, 0, imagesx($new_img) / 2 - $new_width / 2, 0, $new_width, $new_height);

            if ($this->type === IMAGETYPE_PNG) {
                    $img = imagecreatefrompng($this->filename);
                    $newImage = imagecreatetruecolor($new_width,$new_height);

                    imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage, true);
                    imagecopyresampled($newImage,$img,0,0,0,0,$new_width,$new_height,imagesx($img),imagesy($img));
                    imagepng($newImage, $this->filename);
            } else {
                imagejpeg($blank_image, $this->filename, 90);
            }
            imagedestroy($new_img);
            imagedestroy($img);
            imagedestroy($blank_image);
        }
    }

    public function getUniqueName($dest, $suffix = null)
    {
        $new_name = pathinfo($dest);
//my_var_dump($new_name['dirname']);
//die;
        if (!file_exists($new_name['dirname'])) {
            mkdir($new_name['dirname'], 0777, true);
        }
        $dest = $new_name['dirname'] . '/' . Data::sanitize($new_name['filename'], false, false) . ($suffix ? "-$suffix" : '') . '.' . $new_name['extension'];
        $new_name = pathinfo($dest);
//        my_var_dump($new_name);

        if (file_exists($dest)) {
            $counter = 1;
            while (file_exists($new_name['dirname'] . '/' . $new_name['filename'] . $counter . ($suffix ? "-$suffix" : '') . '.' . $new_name['extension'])) {
                $counter++;
            }
            return $new_name['dirname'] . '/' . $new_name['filename'] . $counter . ($suffix ? "-$suffix" : '') . '.' . $new_name['extension'];
        } else {
            return $dest;
        }
    }

    public function move($dest)
    {
        $dest = $this->getUniqueName($dest);
        if(rename ($this->filename, $dest)){
            $this->filename = $dest;
            return true;
        }
    }

    public function copy($dest, $suffix = null)
    {
        $dest = $this->getUniqueName($dest, $suffix);
        if(copy ($this->filename, $dest)){
            return $dest;
        }
    }

    public function generateThumbnails()
    {
        $thumbnails = explode('|', Setting::first('name','thumbnails')->value);
        $this->thumbnails = [];
        foreach ($thumbnails as $thumbnail){
            $dimensions = explode(':', $thumbnail);
            $image = new Image();
            $image->filename=$this->copy($this->filename, $dimensions[0] . 'x' . $dimensions[1]);
            $image->crop($dimensions[0], $dimensions[1]);
            $this->thumbnails[] = $image->filename;
        }
    }

    public static function isUploadedFileImage($filename = null)
    {
        if (is_null($filename)){
            $filename = $_FILES['picture']['tmp_name'];
        }
        $info = getimagesize($filename);
        if ($info === FALSE) {
            $_SESSION['errors'][] = "Не може да се установи типът на изображението";
            return false;
        }
        if (($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
            $_SESSION['errors'][] = "Not a jpeg/png";
            return false;
        }
        return true;
    }

    public static function getAllImages($dir) {

        $result = array();

        $cdir = scandir($dir);
        foreach ($cdir as $key => $value)
        {
            if (!in_array($value,array(".","..")))
            {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
                {
                    $result = array_merge($result, self::getAllImages($dir . $value . '/'));
                }
                else
                {
                    $result[] = $dir . $value;
                }
            }
        }

        return $result;
    }

    public static function getAll($params = null)
    {
        return Thumbnail::query("SELECT * FROM thumbnails GROUP BY picture_id $params");
    }
}