<?php
/**
 * Created by PhpStorm.
 * User: blade
 * Date: 20.10.2018 г.
 * Time: 10:52
 */

namespace App\Controllers;


use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use Core\Data;
use Core\Model;

class Orders
{
    protected $products_per_page = 10;

    public function store()
    {
        $order=null;
        $product=Product::find($_POST['product-id']);
        if($product->variation_name!=null && !postHave('variation')){
            add_error('You must select ' . $product->variation_name);
        }else {
            $where_clause = Data::userIsLoggedIn() ? "user_id=" . $_SESSION['user'] : "session_id='" . $_COOKIE['session_id'] . "'";
            if (postHave('variation')) {
                $order = ProductOrder::query("SELECT * FROM productorders WHERE $where_clause AND product_id=" . $_POST['product-id'] . ' AND variation=\'' . $_POST['variation'] . '\' AND order_id IS NULL');
            } else {
                $order = ProductOrder::query("SELECT * FROM productorders WHERE $where_clause AND product_id=" . $_POST['product-id'] . ' AND order_id IS NULL');
            }

            if (count($order) == 0) {
                $new_order = new ProductOrder();
                $new_order->user_id = Data::userIsLoggedIn() ? $_SESSION['user'] : null;
                $new_order->session_id = !Data::userIsLoggedIn() ? $_COOKIE['session_id'] : null;
                $new_order->product_id = $_POST['product-id'];
                if (postHave('variation')) {
                    $new_order->variation = $_POST['variation'];
                }
                $new_order->qty = $_POST['qty'];
                if (!haveErrors()) {
                    $new_order->save();
                    add_message('The product was successfully added to cart');
                }
            } else {
                $new_order = ProductOrder::find($order[0]->id);
                $new_order->qty += $_POST['qty'];
                $new_order->update();
                add_message('The product was successfully added to cart');
            }
        }
        redirect_back();
    }

    public function removeOrderedProduct()
    {
        $order=ProductOrder::find($_POST['order-id']);
        if((Data::userIsLoggedIn() && $order->user_id===$_SESSION['user']) || $order->session_id===session_id()){
            $order->delete();
            $unfinished_orders=ProductOrder::getUnfinishedOrdersForUser();
            $total_price=getTotalPriceForAllOrders($unfinished_orders);
            echo $total_price==0 ? '' : printPrice($total_price);
            echo '<script>';
            if(count($unfinished_orders)>0) {
                echo '$("#qty").html("' . count($unfinished_orders) . '")';
            }else {
                $text='<p style="text-align: center">Your cart is empty</p>';
                echo '$("#qty").html("0");';
                echo '$("#qty").hide();';
                echo '$(".shopping-cart-list").html("<p style=\"text-align: center\">Your cart is empty</p>");';
            }
            echo '</script>';
        }
    }

    public function index()
    {
        $this->page_num = (isset($_GET['page']) && isInteger($_GET['page']) ? $_GET['page'] : 0);
        $from_product = $this->page_num * $this->products_per_page;
        $order_id=isset($_GET['order-id']) && isInteger($_GET['order-id']) ? $_GET['order-id'] : null;
//        my_var_dump(get_enum_values('orders','status'));
        $status_=get_enum_values('orders','status');
//        my_var_dump($status);
        if($order_id != null){
            $status=isset($_GET['status']) && in_array($_GET['status'], $status_) ? "AND status='{$_GET['status']}'" : null;
            $orders = ProductOrder::queryToSpecificClass("
                            SELECT orders.id, firstname, lastname, email, created_at,
                            city, address, status, 
                            SUM(price*qty) AS total_price, SUM(qty) AS num_items
                            FROM productorders as po
                            INNER JOIN orders
                            ON po.order_id=orders.id      
                            WHERE orders.id=$order_id           
                            $status       
                            GROUP BY po.order_id
            ");
            $this->products_num = 1;
        }else {
            $status=isset($_GET['status']) && in_array($_GET['status'], $status_) ? "WHERE status='{$_GET['status']}'" : null;
            $orders = ProductOrder::queryToSpecificClass("
                            SELECT orders.id, firstname, lastname, email, created_at, city, address, status, 
                            SUM(price*qty) AS total_price, SUM(qty) AS num_items
                            FROM productorders as po
                            INNER JOIN orders
                            ON po.order_id=orders.id 
                            $status                       
                            GROUP BY po.order_id
                            ORDER BY created_at DESC
                            LIMIT $from_product, $this->products_per_page
            ");

            $this->products_num = count(Model::query("
                            SELECT orders.id 
                            FROM productorders as po
                            INNER JOIN orders
                            ON orders.id=po.order_id
                            GROUP BY orders.id"));
        }

//        my_var_dump( $this->products_num);
        return view('admin.list-orders', [
            'admin_orders'=> $orders,
            'statuses' => $status_,
            'pagination' => paginator($this->page_num, ceil($this->products_num / $this->products_per_page)),
            ]);
    }

    public function show($id)
    {
        $orders= Order::query("SELECT * FROM orders WHERE id=$id")[0];

        $product_orders=ProductOrder::query("
                        SELECT po.id, p.title, p.picture_id, po.variation, po.price, po.qty, 
                        p.variation_name, p.variation_values
                        FROM productorders AS po
                        INNER JOIN products AS p
                        ON po.product_id=p.id                        
                        WHERE order_id=$id
        ");
        return view('admin.edit-order', [
            'admin_orders'=> $orders,
            'product_orders' => $product_orders,
        ]);
    }

    public function update($id)
    {
        $order=Order::find($id);

//        my_var_dump($_POST);
        $firstname=filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING);
        $lastname=filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING);
        $email=filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $address=filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $city=filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
        $zip=filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_STRING);
        $phone=filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);

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
            $order->firstname=$firstname;
            $order->lastname=$lastname;
            $order->email=$email;
            $order->address=$address;
            $order->city=$city;
            $order->zip=$zip;
            $order->phone=$phone;
            $order->status=$_POST['status'];
            $order->update();

            add_message('Your order has been successfully completed.');
            redirect_back();
        }
    }

}