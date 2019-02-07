<?php


namespace App\Controllers;

use App\Models\Order;
use App\Models\Picture;
use App\Models\Setting;
use App\Models\Thumbnail;
use App\Models\User;
use Core\Image;

class Admin
{

    public function deleteFile()
    {
        $thumbnail = Thumbnail::first('path', $_POST['filename']);
        $file_for_delete = Picture::find($thumbnail->picture_id);
        $file_for_delete->delete();
    }

    public function index()
    {
        $new_orders = Order::count("WHERE status='processing'");
        $user_registrations = User::count();
//        my_var_dump($user_registrations);
        return view('admin.dashboard', [
            'new_orders' => $new_orders,
            'user_registrations' => $user_registrations,
        ]);
    }

    public function getPictures()
    {
        $images = Image::getAll("ORDER BY id DESC");
        $html = '<div id="proba">';
        $html .= '<form id="product-picture" method="post"><select class="image-picker" id="select-picture" name="picture">';
        $html .= '<option disabled selected></option>';
        foreach ($images as $key => $image) {
            $html .= '<option data-img-src="' . $image->path . '" ' . ($key == 0 ? 'data-img-class="first" ' : '') . ' value="' . $image->picture_id . '" ' . (isset($_POST['product-picture-id']) && $image->picture_id == $_POST['product-picture-id'] ? 'selected' : '') . '></option>';
        }
        $html .= '</select>' . csrf() . '</form>';
        $html .= '<script>$(".image-picker").imagepicker({hide_select:  true,})</script>';

        $html .= "<script>
                    $('.delete-file').on('click', function() {
                        var filename=$(this).parent().find(\"img\").attr(\"src\");
                        var delete_file=$('#filename').attr('value', filename);
                        var li=$(this).closest('li');
                        $('#form-delete-file').submit();

                        $('#form-delete-file').on('submit', function(e1){
                            e.preventDefault();
                            $.ajax({
                                url:\"/admin/delete-file/\",
                                method:\"POST\",
                                data:new FormData(this),
                                contentType:false,
                                //cache:false,
                                processData:false,
                                success:function(data)
                                {
                                    li.remove();
                                }
                            })
                        });

                    });

                        $('#save-changes').on('click', function (e) {
                            $('#product-picture').submit();
                        })
                        $(document).on('click', '#pictures', function(){
                            $(\"#product-picture\").submit();
                        });

            $('#product-picture').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url:\"/admin/set-product-picture/\",
                    method:\"POST\",
                    data:new FormData(this),
                    contentType:false,
                    //cache:false,
                    processData:false,
                    success:function(data)
                    {
                        $('#text1').html('');
                        $('#profile-picture').html(data);
                    }
                })
            })
            </script>
            </div>";
        echo $html;
    }


    public function selectPicture()
    {
        $images = Image::getAll("ORDER BY id DESC");
        $csrf = csrf();
        $suffix = $_POST['suffix'];
        $selected_id = $_POST['selected-id-' . $suffix];
        $html = "
            <form id='form-$suffix' method='post' style='height: 500px; overflow-y: scroll;'>
                <input type=\"hidden\" name=\"element-$suffix\" value=\"$suffix\">
                <input type=\"hidden\" id=\"selected-id-$suffix\" name=\"selected-id-$suffix\" value=\"$selected_id\">
                <input type=\"hidden\" name=\"suffix\" value=\"$suffix\">
                <select class='image-picker' id='select-$suffix' name='select-$suffix'>
                    <option disabled selected></option>";
        foreach ($images as $key => $image) {
            $html .= '<option data-img-src="' . $image->path . '" ' .
                ($key == 0 ? 'data-img-class="first" ' : '') . ' value="' . $image->picture_id . '" ' .
                (isset($_POST['selected-id-' . $suffix]) && $image->picture_id == $_POST['selected-id-' . $suffix]
                    ? 'selected' : '') . '>' . $image->path . '</option>';
        }
        $html .= "
                </select>
                $csrf
            </form>
            <script>
            $('.image-picker').imagepicker({hide_select:  true});
            $('#save-changes-$suffix').on('click', function() {
                $('#$suffix').attr('src', $(\"#select-$suffix option:selected\").text());;
                $('#picture-id-$suffix').val($('#select-$suffix').val());
                $('#selected-id-$suffix').val($('#select-$suffix').val());
            });
        $('#form-$suffix').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url:\"/admin/select-picture/\",
                method:\"POST\",
                data:new FormData(this),
                contentType:false,
                //cache:false,
                processData:false,
                success:function(data)
                {
                    $('#div-$suffix').html(data);                    
                }
            })
        });
            </script>";
        echo $html;
    }

    public function getGalleryPictures()
    {
        $images = Image::getAll("ORDER BY id DESC");
        $html = '<form id="product-picture1" method="post"><select class="image-picker" name="picture1[]" multiple="multiple">';
        $html .= '<option disabled selected></option>';
        foreach ($images as $key => $image) {
            $html .= '<option data-img-src="' . $image->path . '" ' . ($key == 0 ? 'data-img-class="first" ' : '') . ' value="' . $image->picture_id . '" ' . (isset($_POST['gallery-pictures']) && in_array($image->picture_id, $_POST['gallery-pictures']) ? 'selected' : '') . '></option>';
        }
        $html .= '</select>' . csrf() . '</form>';
        $html .= '<script>$(".image-picker").imagepicker({hide_select:  true})</script>';
        $html .= "<script>$('#save-changes1').on('click', function (e) {
            $('#product-picture1').submit();
        })
        $('#product-picture1').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url:\"/admin/set-gallery-pictures/\",
                method:\"POST\",
                data:new FormData(this),
                contentType:false,
                //cache:false,
                processData:false,
                success:function(data)
                {
                    $('#text').html('');
                    $('#profile-picture1').html(data);
                }
            })
        })
        </script>";
        echo $html;

    }

    public function setGalleryPictures()
    {
        $csrf = csrf();
        if (isset($_POST['picture1'])) {
            echo "<script>
            $('#product-picture3').html('$csrf');
            </script>";
            foreach ($_POST['picture1'] as $picture) {
                $pic = Picture::find($picture)->id;
                echo '<div class="col-md-6" style="padding-left: 0; padding-right: 0;">';
                echo '<img src="' . Thumbnail::first('picture_id', $pic)->path . '" style="width:100%; padding: 0 5px 3px 0;">';
                echo "</div>";
                echo "<script>
            var b ='<input type=\"hidden\" id=\"gallery-pictures[]\" name=\"gallery-pictures[]\" value=\"$picture\">';
            $(\"#product-picture3\").append(b);
            $(\"#text\").append(b);
                    </script>";
            }
        } else {
            echo "<script>
            $('#product-picture3').html('$csrf');
            $(\"#text\").html('');
            </script>
            <div class=\"col-md-6\" style=\"padding-left: 0; padding-right: 0;\">
            </div>
            <script>
            </script>";
        }
    }

    public static function valueOf($value)
    {
        return Setting::haveRow('name', $value, 'value');
    }

    public function settings()
    {
        $picture_dimensions = Setting::getPictureDimensions();

        $thumbnails = self::valueOf('thumbnails');
        if ($thumbnails == null) {
            $thumbnails = ':|:';
        }
        $thumbnails = explode('|', $thumbnails);

        $currency_symbol = self::valueOf('currency-symbol');
        $position_symbol = self::valueOf('position-symbol');
        $currency_with_interval = self::valueOf('currency-with-interval');
        $icon_id = self::valueOf('icon');
        $new_product = self::valueOf('new-product');
        $site_title = self::valueOf('site-title');
        $footer_text = self::valueOf('footer-text');

        return view('admin.settings', [
            'picture_dimensions' => $picture_dimensions,
            'thumbnails' => $thumbnails,
            'currency_symbol' => $currency_symbol,
            'position_symbol' => $position_symbol,
            'currency_with_interval' => $currency_with_interval,
            'icon_id' => $icon_id,
            'new_product' => $new_product,
            'site_title' => $site_title,
            'footer_text' => $footer_text,
        ]);
    }

    public function priceSettings()
    {
        $currency_symbol = Setting::first('name', 'currency-symbol');
        if ($currency_symbol) {
            $price = $currency_symbol;
            $price->name = 'currency-symbol';
            $price->value = $_POST['currency-symbol'];
            $price->update();
        } else {
            $price = new Setting();
            $price->name = 'currency-symbol';
            $price->value = $_POST['currency-symbol'];
            $price->save();
        }
    }

    public static function thumbnailsSettings()
    {
        $str = [];
        for ($i = 0; $i < count($_POST['width']); $i++) {
            if (!isInteger($_POST['width'][$i]) || !isInteger($_POST['height'][$i])) {
                add_error('Some of thumbnails contain invalid values');
                return;
            } elseif ($_POST['width'][$i] !== '' && $_POST['height'][$i]) {
                $str[] = $_POST['width'][$i] . ':' . $_POST['height'][$i];
            }
        }
        $thumbnails = Setting::first('name', 'thumbnails');
        if (!isset($thumbnails)) {
            $thumbs = new Setting();
            $thumbs->name = 'thumbnails';
            $thumbs->value = implode('|', $str);
            $thumbs->save();
        } else {
            $thumbnails->value = implode('|', $str);
            $thumbnails->update();
        }
    }

    public function storeSettings()
    {
        $picture_width = filter_input(INPUT_POST, 'picture-width', FILTER_VALIDATE_INT);
        $picture_height = filter_input(INPUT_POST, 'picture-height', FILTER_VALIDATE_INT);
        $new_product = getFromPost('new-product');
        if (!$picture_width) {
            add_error('Invalid value for picture width');
        }
        if (!$picture_height) {
            add_error('Invalid value for picture height');
        }
        if ($new_product && !isInteger($new_product)) {
            add_error('Invalid value for new product');
        }

        self::thumbnailsSettings();
        if (!haveErrors()) {
            Setting::addOrUpdate('picture-dimensions', $_POST['picture-width'] . '|' . $_POST['picture-height']);
            Setting::addOrUpdate('icon', $_POST['picture-id-icon']);
            Setting::addOrUpdate('logo', $_POST['picture-id-logo']);
            $currency_with_interval = isset($_POST['currency-with-interval']) ? 'yes' : 'no';
            Setting::addOrUpdate('currency-symbol', $_POST['currency-symbol']);
            Setting::addOrUpdate('position-symbol', $_POST['position-symbol']);
            Setting::addOrUpdate('currency-with-interval', $currency_with_interval);
            Setting::addOrUpdate('new-product', $new_product);
            Setting::addOrUpdate('site-title', $_POST['site-title']);
            Setting::addOrUpdate('footer-text', $_POST['footer-text']);

            add_message('Data was successfully updated');
        }
        redirect_back();
    }

}