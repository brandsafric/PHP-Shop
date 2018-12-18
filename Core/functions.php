<?php
use eftec\bladeone\BladeOne;

$currency_symbol = $currency_position = $with_interval = null;

function redirect($url)
{
    header("Location: $url");
    exit();
}

function redirect_back()
{
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

function add_message($message)
{
    $_SESSION['messages'][] = $message;
}

function add_error($error)
{
    $_SESSION['errors'][] = $error;
}

function haveErrors()
{
    return count ($_SESSION['errors']) > 0;
}

function haveMessages()
{
    return count ($_SESSION['messages']) > 0;
}

function dateTimeNow()
{
    return date("Y-m-d H:i:s");
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function server()
{
    return $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
}

function isInteger($input)
{
    return(ctype_digit(strval($input)));
}

function init()
{
    if (!\Core\Data::userIsLoggedIn()){
        if (isset($_COOKIE['remember_me'])) {
            $remember_token = $_COOKIE['remember_me'];
            $user = App\Models\User::first('remember_token', $remember_token);
            if ($user != null) {
                session_start();
                \Core\Data::setUserData($user->id);
            }
        }elseif(isset($_COOKIE['session_id'])){
            session_id($_COOKIE['session_id']);
        }else{
            session_start();
            session_regenerate_id();
            setcookie('session_id', session_id(), time()+60*60*24*30, server() );
        }
    }elseif(\Core\Auth::user()->status === 'blocked'){
        \Core\Auth::logOut();
    }

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $_SESSION['old']=$_POST;
        //unset($_POST);
    }

    if(!isset($_SESSION['errors'])) {
        $_SESSION['errors'] = [];
    }
    if(!isset($_SESSION['messages'])){
        $_SESSION['messages'] = [];
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(!isset($_POST['_token'])){
            add_error('Missing token');
            redirect_back();
        }elseif(!hash_equals($_SESSION['token'], $_POST['_token'])) {
            add_error('Non-matching token');
            redirect_back();
        }
    }

    setlocale(LC_ALL, 'bg_BG');
    date_default_timezone_set('Europe/Sofia');

    error_reporting(E_ALL);
    set_error_handler('Core\Error::errorHandler');
    set_exception_handler('Core\Error::exceptionHandler');

    global $currency_symbol, $currency_position, $with_interval;
    $currency_symbol=\App\Models\Setting::haveRow('name', 'currency-symbol', 'value');
    $currency_position=\App\Models\Setting::haveRow('name', 'position-symbol', 'value');

}


function delete_file($name)
{
    $str = '.' . $name;
    if (substr( $name, 0, 7 ) === "http://" || substr( $name, 0, 8 ) === "https://") {
        $str = substr($name, strpos($name, '://') + 3);
        $str = '.' . substr($str, strpos($str, '/'));
    }
    unlink($str);
}


function view($view, $args = [])
{
    $blade=new BladeOne('../views', '../views/compiled', BladeOne::MODE_DEBUG);
    try {
        $args['categories']=\App\Models\Category::all('ORDER BY id');

        global $currency_symbol, $currency_position, $with_interval;
        $with_interval=\App\Models\Setting::haveRow('name', 'currency-with-interval', 'value');
        $args['orders']=\App\Models\ProductOrder::getUnfinishedOrdersForUser();
        $args['user']=\Core\Data::userIsLoggedIn() ? \Core\Auth::user() : null;

        $icon=\App\Models\Setting::haveRow('name', 'icon', 'value');
        $args['icon'] = $icon!=null ? \App\Models\Picture::find($icon)->path : null;

        $logo=\App\Models\Setting::haveRow('name', 'logo', 'value');
        $args['logo'] = $logo!=null ? \App\Models\Picture::find($logo) : null;

        $args['site_title']=\App\Models\Setting::haveRow('name', 'site-title', 'value');
        $args['footer_text']=\App\Models\Setting::haveRow('name', 'footer-text', 'value');


        if($args['orders']!=null) {
            $args['total_price'] = getTotalPriceForAllOrders($args['orders']);
        }

        echo $blade->run($view, $args);
        if($_SERVER['REQUEST_METHOD']!='POST'){
            unset($_SESSION['old']);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    $_SESSION['errors']=null;
    $_SESSION['messages']=null;
}

function csrf()
{
    if(!isset($_SESSION['token'])){
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(64));
    }
    return '<input type="hidden" name="_token" value="' . $_SESSION['token'] . '">';
}

function old($name, $default = null)
{
    if(haveErrors() && isset($_SESSION['old'][$name])){
        return $_SESSION['old'][$name];
    }else{
        return $default;
    }
}

function getTotalPriceForAllOrders($orders)
{
    $total_price = 0;
    foreach ($orders as $order) {
        $total_price += ($order->promo_price !== null ? $order->qty * $order->promo_price : $order->qty * $order->price);
    }
    return $total_price;
}

function user()
{
    return \Core\Auth::user();
}

function getCategories($categories, $parent)
{
    $html="<ul class='levelTwo'>";
    foreach ($categories as $key=>$category){
        if($category->parent_id==$parent){
            if (!hasSubCategories($categories, $category->id)){
                $html.="<li>
                            <a href='/category/$category->alias'>$category->name</a>
                        </li>";
            }else{
                $html.="<li>
                            <a href='/category/$category->alias'>$category->name<i class='fa fa-angle-right fa-fw'></i></a>";
                $html.=getCategories($categories, $category->id);
                $html.="</li>";
            }

        }
    }

    $html.="</ul>";
    return $html;
}

function hasSubCategories($categories, $id)
{
    foreach ($categories as $category){
        if($category->parent_id==$id){
            return true;
        }
    }
    return false;
}

function generateMenu($categories)
{
    $html="<div class='navContainer'>
    <nav>
        <ul>
            <li>
                <a href='#'> Categories <i class='fa fa-angle-down fa-fw'></i></a>";
                $html.=getCategories($categories, null);
            $html.="</li>
        </ul>
    </nav>
</div>";
    return $html;
}


function printPrice($price)
{
    global $currency_symbol, $currency_position, $with_interval;
    return $currency_position=='left' ? $currency_symbol . ($with_interval == 'yes' ? ' ' : '') . $price : $price . ($with_interval == 'yes' ? ' ' : '') . $currency_symbol;
}

function postHave($name)
{
    return isset($_POST[$name]) && trim($_POST[$name])!='';
}

function postOrNull($name)
{
    return postHave($name) ? trim($_POST[$name]) : NULL;
}

function post($name)
{
    return isset($_POST[$name]) ? $_POST[$name] : NULL;
}

function getFromPost($name)
{
//    return isset($_POST[$name]) ? trim($_POST[$name]) : NULL;
    return isset($_POST[$name]) && trim($_POST[$name])!='' ? trim($_POST[$name]) : NULL;
}


function my_var_dump($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die;
}

function get_enum_values( $table, $field )
{
    $type = \Core\Model::query( "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" )[0]->Type;
    preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
    $enum = explode("','", $matches[1]);
    return $enum;
}

function get()
{
    $result = '';
    foreach ($_GET as $key => $index){
        if ($key != 'page' && $index != '') {
            $result .= "&$key=$index";
        }
    }
    return $result;
}


function paginator(&$current_page, $page_num)
{
    $query_string = get();
    $actual_link = explode('?', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    $actual_link = $actual_link[0];
    $actual_link = substr ($actual_link, 0, strlen($actual_link));

    $html = '';
    $first_page = $last_page = true;
//    my_var_dump($page_num);
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