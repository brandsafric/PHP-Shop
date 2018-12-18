<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOrder;
use Core\Auth;
use Core\Model;

class Home extends \Core\Controller
{
    protected $products_per_page = 2;
    protected $products_num;
    protected $page_num;

    public function index($category = null)
    {
        $products=null;
        $sort=self::sort();
        $products_per_page=self::productsPerPage();
        $homepage=true;
        if (!isset($category)) {
            $products = Product::query("
                      SELECT p.id, title, picture_id, p.updated_at, price, 
                      promo_price, variation_name, variation_values, AVG(r.rating) as rating
                      FROM products AS p
                      LEFT JOIN reviews AS r
                      ON p.id=r.product_id
                      GROUP BY p.id
                      ORDER BY p.updated_at DESC 
                      LIMIT $products_per_page");
            $title='Homepage';
            $header='Latest Products';
        }else {
            $categories = implode(',', Category::childCategories($category));
            $this->page_num = (isset($_GET['page']) && isInteger($_GET['page']) ? $_GET['page'] : 0);
            $from_product = $this->page_num * $products_per_page;
            $homepage=false;

            $products = Product::query("
                      SELECT p.id, title, picture_id, p.updated_at, price, 
                      promo_price, variation_name, variation_values, AVG(r.rating) as rating
                      FROM products AS p
                      LEFT JOIN reviews AS r
                      ON p.id=r.product_id
                      WHERE category_id IN ($categories)
                      GROUP BY p.id
                      ORDER BY p.$sort 
                      LIMIT $from_product, $products_per_page");

            $this->products_num=Model::query("
                      SELECT COUNT(*) 
                      FROM products
                      WHERE category_id IN ($categories)")[0]->{'COUNT(*)'};
            $title=ucfirst($category);
            $header=$title;

        }
        return view('home',[
            'products' => $products,
            'title' => $title,
            'header' => $header,
            'sort' => isset($_COOKIE['SortBy']) ? $_COOKIE['SortBy'] : null,
            'products_per_page' => isset($_COOKIE['ProductsPerPage']) ? $_COOKIE['ProductsPerPage'] : null,
            'homepage' => $homepage,
            'pagination' => $category!=null ? $this->paginator($this->page_num, ceil($this->products_num / $products_per_page)) : null,
        ]);
    }

    public static function sort()
    {
        $sort_by = isset($_COOKIE['SortBy']) ? $_COOKIE['SortBy'] : null;
        $sort=null;
        switch ($sort_by){
            case 'lower-price' : $sort='price ASC';break;
            case 'higher-price' : $sort='price DESC';break;
            default : $sort='updated_at DESC';break;
        }
        return $sort;
    }

    public static function productsPerPage()
    {
        $cookie_value = isset($_COOKIE['ProductsPerPage']) ? $_COOKIE['ProductsPerPage'] : null;
        $products_per_page=null;
        switch ($cookie_value){
            case '24' : $products_per_page='24';break;
            case '36' : $products_per_page='36';break;
            default : $products_per_page='12';break;
        }
        return $products_per_page;
    }

    public function search()
    {
        $sort=self::sort();
        $products=[];
        $from_product=null;
        $products_per_page=self::productsPerPage();
        if(!isset($_GET['q']) || (isset($_GET['q']) && $_GET['q']=='')) {
            $products=[];
        }else {
            if (isset($_GET['q'])) {
                $this->page_num = (isset($_GET['page']) && isInteger($_GET['page']) ? $_GET['page'] : 0);
                $from_product = $this->page_num * $products_per_page;

                $search = $_GET['q'];
                if (!isset($_GET['category']) || (isset($_GET['category']) && $_GET['category'] == '')) {
                    $products = Model::query("
                                SELECT p.id, title, picture_id, p.updated_at, price, 
                                promo_price, variation_name, variation_values, AVG(r.rating) as rating
                                FROM products AS p
                                LEFT JOIN reviews AS r
                                ON p.id=r.product_id
                                WHERE title LIKE '%$search%'
                                GROUP BY p.id
                                ORDER BY p.$sort 
                                LIMIT $from_product, $products_per_page
                                ");

                    $this->products_num = Model::query("
                      SELECT COUNT(*) 
                      FROM products
                      WHERE title LIKE '%$search%'
                      ")[0]->{'COUNT(*)'};

                } elseif (isset($_GET['category']) && $_GET['category'] != '') {
                    $categories = implode(',', Category::childCategories($_GET['category']));
                    $products = Model::query("
                                SELECT p.id, title, picture_id, p.updated_at, price, promo_price, 
                                variation_name, variation_values, AVG(r.rating) as rating
                                FROM products AS p
                                LEFT JOIN reviews AS r
                                ON p.id=r.product_id
                                WHERE category_id IN ($categories)
                                AND title LIKE '%$search%' 
                                GROUP BY p.id
                                ORDER BY p.$sort 
                                LIMIT $from_product, $products_per_page
                                ");

                    $this->products_num = Model::query("
                                SELECT COUNT(*) 
                                FROM products
                                WHERE category_id IN ($categories)
                                AND title LIKE '%$search%' 
                                ORDER BY products.created_at DESC")[0]->{'COUNT(*)'};

                }
            } else {
                redirect_back();
            }
        }
//        my_var_dump(count($products));
        return view('search', [
            'products' => $products,
            'title' => 'Search',
            'sort' => isset($_COOKIE['SortBy']) ? $_COOKIE['SortBy'] : null,
            'products_per_page' => isset($_COOKIE['ProductsPerPage']) ? $_COOKIE['ProductsPerPage'] : null,
            'search' => (isset($_GET['q']) ? $_GET['q'] : ''),
            'from_product' => $from_product,
            'to_product' => $from_product + count($products),
            'pagination' => $this->paginator($this->page_num, ceil($this->products_num / $products_per_page)),
        ]);
    }

    public function setSortAndProductsPerPage()
    {
        $types=['newest', 'lower-price', 'higher-price'];
        $sort_by= in_array($_POST['sort'], $types) ? $_POST['sort'] : 'newest';
        setcookie('SortBy', $sort_by, time()+60*60*24*30, server());

        $product_per_page_array=['12', '24', '36'];
        $products_per_page= in_array($_POST['products-per-page'], $product_per_page_array)
            ? $_POST['products-per-page'] : '12';
        setcookie('ProductsPerPage', $products_per_page, time()+60*60*24*30, server());

        redirect_back();
    }

    public static function get()
    {
        $result = '';
        foreach ($_GET as $key => $index){
            if ($key != 'page' && $index != '') {
                $result .= "&$key=$index";
            }
        }
        return $result;
    }


    public function paginator(&$current_page, $page_num)
    {
        $query_string = self::get();
        $actual_link = explode('?', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        $actual_link = $actual_link[0];
        $actual_link = substr ($actual_link, 0, strlen($actual_link));

        $html = '';
        $first_page = $last_page = true;
        if ($page_num > 0) {
            if ($current_page == 0) {
                $first_page = false;
            }
            if ($current_page + 1 >= $page_num) {
                $last_page = false;
            }

            $html .= '<!--Pagination -->
                    <nav aria-label="pagination example">
                    <ul class="pagination pagination-circle pg-blue mb-0 justify-content-center">
                    <!--First-->
                         <li class="page-item' . (!$first_page ? ' disabled' : '') . '"><a class="page-link" ' .
                ($first_page ? ' a href="' . $actual_link . '?page=0' . $query_string . '"' : '') . '>First</a></li>';


            $visible_pages_num = 3;

            if ($current_page >= $page_num) {
                $current_page = $page_num - 1;
            }
            $pages_from = $current_page - floor($visible_pages_num / 2);
            if ($pages_from < 0) {
                $pages_from += abs($pages_from);
            } elseif ($current_page == $page_num - 1) {
                $pages_from = $page_num - $visible_pages_num;
            }
            $pages_to = $pages_from + $visible_pages_num;
            $html .= '<!--Numbers-->';
            for ($i = $pages_from; $i < $pages_to; $i++) {
                if ($i >= 0 && $i < $page_num) {
                    if ($current_page == $i && $i == (isset ($_GET['page']) ? $_GET['page'] : 0)) {
                        $html .= '<li class="page-item active"><a class="page-link">' . ($i + 1) . '</a></li>';
                    }else{
                        $html .= '<li class="page-item"><a class="page-link" href="' . $actual_link . "?page=$i$query_string" . '">' . ($i + 1) . '</a></li>';
                    }
                    $pages[$i]['index'] = $i;
                }
            }
                $html.='<!--Last-->
                <li class="page-item' . (!$last_page ? ' disabled' : '') . '"><a class="page-link" ' .
                ($last_page ? ' a href="' . $actual_link . '?page=' . ($page_num - 1) . $query_string : '') . '">Last</a></li>';
        }
        return $html;
    }


}
