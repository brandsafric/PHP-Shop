<?php

namespace App\Controllers;


use App\Models\Picture;
use App\Models\Product;
use App\Models\ProductPicture;
use App\Models\ProductPictures;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Thumbnail;
use Core\Model;

class Products extends \Core\Controller
{

    public function show($id)
    {
        $product=Product::find($id);
        if($product==null){
            return view('404');
        }
        $product=Product::query("
                    SELECT products.*, AVG(reviews.rating) as rating 
                    FROM products
                    INNER JOIN reviews
                    ON products.id=reviews.product_id
                    AND products.id=$id
                    ")[0];

//        my_var_dump($product);


        $pictures=ProductPicture::query("
            SELECT path, pictures.id 
            FROM productPictures 
            INNER JOIN pictures 
            ON productPictures.picture_id=pictures.id 
            WHERE productPictures.product_id=$id
            ");

        $pictures1=Picture::where('id', $product->picture_id);
        array_splice( $pictures, 0, 0, $pictures1 ); // splice in at position 3

        $thumbnails=[];
        foreach ($pictures as $picture){
                $thumbnails[]=Thumbnail::first('picture_id', $picture->id)->path;
        }


        $reviews = Review::where('product_id', $id);
        return view('product', [
            'product' => $product,
            'pictures' => $pictures,
            'thumbnails' => $thumbnails,
            'reviews' => $reviews,
            ]);
    }


    public function indexAdmin()
    {
        $products=Product::query("SELECT products.*, thumbnails.path 
                                        FROM products 
                                        LEFT JOIN thumbnails
                                        ON thumbnails.picture_id=products.picture_id
                                        GROUP BY products.id
                                        ORDER BY updated_at DESC");
        return view('admin.list-products', ['products' => $products]);
    }


    public function edit($id)
    {
        $pictures=Thumbnail::query("SELECT pp.id, t.path, pp.picture_id FROM thumbnails as t
                                          INNER JOIN productpictures as pp
                                          ON pp.picture_id=t.picture_id
                                          WHERE pp.product_id=$id
                                          GROUP BY pp.picture_id");

        return view('admin.edit-product', [
            'product'=>Product::find($id),
            'pictures'=>$pictures,
            'picture_dimensions' => Setting::getPictureDimensions()]);
    }


    public function create()
    {
        return view('admin.add-product', ['picture_dimensions' => Setting::getPictureDimensions()]);
    }

    public function destroy($id)
    {
        $product=Product::find($id);
        $product->delete();
        add_message('Product was delete successfully');
        return redirect_back();
    }

    public function update($id)
    {
        if(!postHave('title')){
            add_error('Title can not be empty');
        }
        if(!postHave('category-id')){
            add_error('You must select category');
        }
        if(!postHave('price')){
            add_error('You must enter price');
        }elseif(postHave('promo-price') && !($_POST['promo-price']>0 && $_POST['promo-price']< $_POST['price'])) {
            add_error('The promotional price should be lower than the regular price.');
        }
        if(!postHave('datetime')){
            add_error('You must select datetime');
        }

        if(!postHave('variation-name'))

        if(!haveErrors()) {
            $product = Product::find($id);
            $product->title = $_POST['title'];
            $product->category_id=$_POST['category-id'];
            $product->description=$_POST['description'];
            $product->price=$_POST['price'];
            $product->promo_price=($_POST['promo-price']!='' ? $_POST['promo-price'] : NULL);
            $product->variation_name=($_POST['variation-name']!='' ? $_POST['variation-name'] : NULL);
            $product->variation_values=($_POST['variation-value']!='' ? $_POST['variation-value'] : NULL);
            $product->picture_id=isset($_POST['picture-id-picture']) ? $_POST['picture-id-picture'] : NULL;
            $product->updated_at=$_POST['datetime'];

            $product->update();
            ProductPicture::query1("DELETE FROM productpictures WHERE product_id=$id");
            if (isset($_POST['gallery-pictures'])) {
                foreach ($_POST['gallery-pictures'] as $picture) {
                    $pcx = new ProductPicture();
                    $pcx->picture_id = $picture;
                    $pcx->product_id = $product->id;
                    $pcx->save();
                }
            }
            add_message('The product was successfully updated');
        }
        redirect_back();
//        return $this->edit($id);
    }

    public function store()
    {
        if(!postHave('title')){
            add_error('Title can not be empty');
        }
        if(!postHave('category-id')){
            add_error('You must select category');
        }
        if(!postHave('price')){
            add_error('You must enter price');
        }elseif(postHave('promo-price') && !($_POST['promo-price']>0 && $_POST['promo-price']< $_POST['price'])) {
            add_error('The promotional price should be lower than the regular price.');
        }

        if (postHave('variation-name')) {
            if (postHave('variation-value')) {

            }
        }
        if(!haveErrors()) {
            $product = new Product();
            $product->title = $_POST['title'];
            $product->price = $_POST['price'];
            $product->promo_price = postOrNull('promo-price');
            $product->availability = 'In Stock';
            $product->description = $_POST['description'];
            $product->category_id = $_POST['category-id'];
            $product->picture_id = postOrNull('picture-id-picture');

            if (postHave('variation-name')) {
                if (postHave('variation-value')) {
                    $variations = explode('|', $_POST['variation-value']);
                    if (count($variations) > 0) {
                        $product->variation_name = $_POST['variation-name'];
                        $product->variation_values = $_POST['variation-value'];
                    }

                }
            }

            $product->save();

            if (isset($_POST['gallery-pictures'])) {
                foreach ($_POST['gallery-pictures'] as $picture) {
                    $pcx = new ProductPicture();
                    $pcx->picture_id = $picture;
                    $pcx->product_id = $product->id;
                    $pcx->save();
                }
            }
            add_message('Successfully added product');
        }
        return redirect_back();
    }

    public function addReview($id)
    {
//        my_var_dump($_POST);
        $name=filter_input(INPUT_POST, 'name');
        $email=filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $text=trim(filter_input(INPUT_POST, 'text'));
        $rating=filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);


        if(!$name){
            add_error('Name field can not be empty');
        }
        if(!$email){
            add_error('Email is invalid');
        }
        if(!$text){
            add_error('Your review text can not be empty');
        }elseif(strlen($text)>500){
            add_error('Your review text length can not be larger than 500 symbols');
        }
        if(!$rating){
            add_error('You must select rating');
        }elseif(!($rating >= 1 && $rating <=5)) {
            add_error('Invalid value for rating');
        }

//my_var_dump($_POST);
        if(!haveErrors()){
            $review=new Review();
            $review->name=$name;
            $review->email=$email;
            $review->text=$text;
            $review->product_id=$id;
            $review->rating=$rating;
            $review->text=$text;
            $review->save();

            add_message('Review was added successfully');
        }

        redirect_back();
    }
}
