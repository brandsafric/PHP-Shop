<?php

namespace App\Controllers;

use App\Models\Picture;
use App\Models\Setting;
use App\Models\Thumbnail;
use Core\Image;
use App\Config;
use Core\Model;

class Pictures extends \Core\Controller
{
    public function create()
    {
        return view('admin.upload-picture', ['picture_dimensions' => Setting::getPictureDimensions()]);
    }

    public function store()
    {
//        my_var_dump($_POST);
        for($i=0; $i<count($_FILES['file']['name']); $i++) {
            if ($_FILES['file']['name'][$i] == '') {
                add_error("Please Select File");
            }else{
                $var = explode(".", $_FILES['file']['name'][$i]);
                $extension = end($var);
                $allowed_type = array("jpg", "jpeg", "png");
                if (!in_array($extension, $allowed_type)) {
                    add_error("Invalid File Format");
                }else{
                    $today = getdate();
                    $path = $today['year'] . '/' . $today['mon'] . '/';
                    $image = new Image($_FILES['file']['tmp_name'][$i]);
//                    my_var_dump($image);
                    if (!$image->move(Config::UPLOAD_PATH . $path . $_FILES['file']['name'][$i])) {
                        add_error("File " . $_FILES['file']['name'][$i] . "was not uploaded");
                    }else{

                        if(isset($_POST['crop']) && $_POST['crop']=='on') {
                            $image->crop($_POST['width'], $_POST['height']);
                        }
                        $image->generateThumbnails();
                        $picture = new Picture();
                        $picture->path='/' . $image->filename;
                        $picture->save();

//                        var_dump($picture->id);
//                        die;
                        foreach ($image->thumbnails as $thumbnail){
                            $thumb = new Thumbnail();
                            $thumb->path='/' . $thumbnail;
                            $thumb->picture_id=$picture->id;
                            $thumb->save();
                        }
                    }
                }
            }
        }
        if(!haveErrors()) {
            add_message('The images were added successfully.');
        }
        redirect_back();
    }


}
