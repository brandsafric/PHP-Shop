<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use Core\Auth;
use Core\Data;
use Core\Model;

class ProductOrders extends \Core\Controller
{

    public function cart()
    {
        if(!Data::userIsLoggedIn()){
            redirect('/');
        }
        return view('cart', ['orders' => ProductOrder::getUnfinishedOrdersForUser()]);
    }

    public function update($id)
    {
        $order = ProductOrder::find($id);
        if($order==null || $order->order_id != null || $order->user_id != Auth::user()->id){
            add_error('Error');
        }

        if(!isInteger($_POST['qty']) || (isInteger($_POST['qty']) && $_POST['qty']<1)){
            add_error('Invalid value for quantity');
        }

        if(haveErrors()){
            redirect_back();
        }

        $order->qty=$_POST['qty'];
        $order->update();
        add_message('Your cart was updated successfully');
        return redirect_back();
    }

    public function updateAdmin($id)
    {
        $order = ProductOrder::find($id);
//        my_var_dump($order);
        if($order==null){
            add_error('Error');
        }

        if(!isInteger($_POST['qty']) || (isInteger($_POST['qty']) && $_POST['qty']<1)){
            add_error('Invalid value for quantity');
        }

        if(haveErrors()){
            redirect_back();
        }

        $order->qty=$_POST['qty'];
        $order->variation=postOrNull('variation-name');
        $order->update();
        add_message('Your cart was updated successfully');
        return redirect_back();
    }

    public function destroy($id)
    {
        $order = ProductOrder::find($id);
        if($order==null || $order->order_id != null || $order->user_id != Auth::user()->id){
            add_error('Error');
            redirect_back();
        }

        $order->delete();
        add_message('This product was successfully deleted');
        return redirect_back();
    }

    public function checkout()
    {
//        if(!Data::userIsLoggedIn()) {
//            return redirect('/');
//        }

        $orders=ProductOrder::getUnfinishedOrdersForUser();
        return view('checkout', ['user' => Auth::user(), 'total_price' => ProductOrder::getTotalPrice($orders)]);

    }

    public function store()
    {

        $orders=ProductOrder::getUnfinishedOrdersForUser();
        if(count($orders)==0) {
            add_error('Your cart is empty');
            redirect_back();
        }
        $firstname=filter_input(INPUT_POST, 'first-name', FILTER_SANITIZE_STRING);
        $lastname=filter_input(INPUT_POST, 'last-name', FILTER_SANITIZE_STRING);
        $email=filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $address=filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $city=filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
        $zip=filter_input(INPUT_POST, 'zip-code', FILTER_SANITIZE_STRING);
        $phone=filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_STRING);

        if(!$firstname){
            add_error('You must enter first name');
        }
        if(!$lastname){
            add_error('You must enter last name');
        }
        if(!$email){
            add_error('Email address is not valid');
        }
        if(!$address){
            add_error('You must enter some address');
        }
        if(!$city){
            add_error('You must enter city');
        }
        if(!$zip || !isInteger($zip)){
            add_error('Zip is not valid');
        }
        if(!$phone){
            add_error('You must enter phone');
        }

        if(haveErrors()) {
            redirect_back();
        }else{
            $order=new Order();
            $order->firstname=$firstname;
            $order->lastname=$lastname;
            $order->email=$email;
            $order->address=$address;
            $order->city=$city;
            $order->zip=$zip;
            $order->phone=$phone;
            $order->save();

            $where_clause = Data::userIsLoggedIn() ? "user_id=" . $_SESSION['user'] : "session_id='" . $_COOKIE['session_id'] . "'";

            $orders = ProductOrder::query("
                        SELECT po.id, p.price, p.promo_price, po.qty  
                        FROM productorders as po
                        INNER JOIN products as p
                        ON po.product_id=p.id 
                        WHERE $where_clause
                        AND po.order_id IS NULL
                        ");

            foreach ($orders as $o){
                $ord=ProductOrder::find($o->id);
                $ord->price = $o->promo_price!=null ? $o->promo_price : $o->price;
                $ord->order_id = $order->id;
                $ord->update();
            }
            add_message('Your order has been successfully completed.');
            return redirect('/');
        }
    }


}
