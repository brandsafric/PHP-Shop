<?php

namespace App\Controllers;

use Core\Auth;
use Core\DB;
use \App\Models\User;
use Core\Data;
use Carbon\Carbon;

class Users extends \Core\Controller
{

    public function index()
    {
        if (Data::userIsLoggedIn() === true) {
            header('Location: /');
        }
        view('signin');
    }

    public function forgot_password()
    {
        if (Data::userIsLoggedIn() === true) {
            header('Location: /');
        }
        View::renderTemplate('Users/forgot-password.html');
    }

    public function forgot_password_post()
    {
        if (Data::userIsLoggedIn() === true) {
            header('Location: /');
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            add_error('Невалиден имейл адрес');
        } else {
            $user = User::where('email', $email);
            if (count($user) > 0) {
                if ($user[0]['status'] != 'blocked') {
                    $user = User::find($user[0]['id']);
                    $user->pass_recovery_token = bin2hex(openssl_random_pseudo_bytes(64));
                    $user->pass_recovery_created_at = date("Y-m-d H:i:s");
                    $user->update();

                    $headers = "From: " . server() . "\r\n";
                    $headers .= "Reply-To: " . $email . "\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                    $message = '<html><body><p style="font-size: 16px">This is a link to recover your password<br>' . server() . '/recovery?token=' . $user->pass_recovery_token . '<br>If you did not require a recover, <strong>DO NOT</strong> press on it</p></body></html>';
                    mail($email, 'Password Recovery', $message, $headers);
                }
            }
            add_message("If there is a user with an email \"{$_POST['email']}\" a letter is sent.");
        }
        View::renderTemplate('Users/forgot-password.html');
    }

    public function recovery_password()
    {
        if (Data::userIsLoggedIn() === true) {
            header('Location: /');
        }

        if (!isset($_GET['token'])) {
            add_error('Missing token');
        } else {
            $token = $_GET['token'];
            $user = User::where('pass_recovery_token', $token);
            if (count($user) === 0) {
                add_error('Unrecognized token');
            } else {
                View::renderTemplate('Users/recovery-password.html');
                exit;
            }
        }

        View::renderTemplate('Posts/index.html');
    }

    public function recovery_password_post()
    {
        if (Data::userIsLoggedIn() === true) {
            header('Location: /');
        }

        if (!isset($_GET['token'])) {
            add_error('Missing token');
        } else {
            $token = $_GET['token'];
            $user = User::where('pass_recovery_token', $token);

            if (count($user) === 0) {
                add_error('Unrecognized token');
            } else {
                $format = 'Y-m-d H:i:s';
                $date1 = Carbon::createFromFormat($format, date($format));
                $date2 = Carbon::createFromFormat($format, $user[0]['pass_recovery_created_at']);

                if ($date1->diffInHours($date2) > 23) {
                    add_error('Expired token');
                } elseif (!have_post('pass1') || !have_post('pass2')) {
                    add_error('Missing password in one of the fields');
                } else {
                    $pass1 = $_POST['pass1'];
                    $pass2 = $_POST['pass2'];
                    if ($pass1 !== $pass2) {
                        add_error('Passwords do not match');
                    } elseif (!User::isValidPassword($pass1)) {
                        add_error('The password must be at least 6 characters long and must contain at least one lowercase, one capital letter and one digit. Letters must be Latin only.');
                    } else {
                        $user = User::find($user[0]['id']);
                        $user->password = password_hash($pass1, PASSWORD_BCRYPT);
                        $user->remember_token = null;
                        $user->pass_recovery_token = null;
                        $user->pass_recovery_created_at = null;
                        $user->update();
                        add_message("The password was successfully updated");
                    }
                }
            }
            redirect('signin');
        }

        View::renderTemplate('Users/recovery-password.html');
    }

    public function logout()
    {
        Auth::logOut();
        header('Location: /');
        exit();
    }

    public function registerIndex()
    {
        if (Data::userIsLoggedIn() === true) {
            header('Location: /');
        }
        view('signup');
    }

    public function register()
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if ($email === '') {
            add_error('The email field can not be blank');
        } elseif (!$email) {
            add_error('Invalid email address');
        }

        $username = strtolower(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_MAGIC_QUOTES));
        if (!preg_match('/^[a-z]+[a-z0-9]{3,14}$/', $username)) {
            add_error('The username should begin with a Latin letter. It must then contain letters and numbers and must contain 4 to 15 characters');
        }

        $pass1 = filter_input(INPUT_POST, 'password');
        $pass2 = filter_input(INPUT_POST, 'confirm_password');

        if (count(DB::query("SELECT email FROM users WHERE email = '$email'")) !== 0) {
            add_error('There is already a user with this email');
        }
        if (count(DB::query("SELECT username FROM users WHERE username = '$username'")) !== 0) {
            add_error('There is already a user with this username');
        }
        if (!User::isValidPassword($pass1)) {
            add_error('The password must be at least 6 characters long and must contain at least one lowercase, one capital letter and one digit. Letters must be Latin only.');
        } elseif ($pass1 !== $pass2) {
            add_error('The passwords do not match');
        }

        if (count($_SESSION['errors']) > 0) {
            return $this->registerIndex();
        } else {
            $crypt_password = password_hash($pass1, PASSWORD_BCRYPT);
            $user = new User();
            $user->email = $email;
            $user->username = $username;
            $user->password = $crypt_password;
            if(count(User::all())==0){
                $user->role='admin';
            }
            $user->save();
            add_message('You have registered successfully');
            redirect('/signin');
        }
    }

    public function signin()
    {
        $result = null;
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if ($email === '') {
            add_error('The email field can not be blank');
        } else {
            if (!$email) {
                $search_column = 'username';
                $email = addslashes(filter_input(INPUT_POST, 'email'));
            } else {
                $search_column = 'email';
            }

            $password = filter_input(INPUT_POST, 'password');
            $result = User::first($search_column, $email);
            if ($result == null) {
                add_error('Wrong credentials');
            } else {
                if ($result->status === 'blocked') {
                    add_error('This user is blocked');
                } elseif (password_verify($password, $result->password) == 0) {
                    add_error('Wrong credentials');
                } else {
                    Data::setUserData($result->id);
                    if (isset($_POST['remember']) && $_POST['remember'] === 'yes') {
                        $remember_token = bin2hex(random_bytes(32));
                        setcookie('remember_me', $remember_token, time() + 60 * 60 * 24 * 30);
                        $user = User::find($result->id);
                        $user->remember_token = $remember_token;
                        $user->update();
                    }
                    add_message('Welcome ' . $result->username);
                    return redirect('/');
                }
            }
        }
        return $this->index();
    }


    public function edit()
    {
        if (!Data::userIsLoggedIn() === true) {
            header('Location: /');
        }
        view('edit-profile');
    }

    public function editProfile()
    {
        $_SESSION['errors'] = [];
        if (!Data::userIsLoggedIn() === true) {
            header('Location: /');
        }
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $sex = filter_input(INPUT_POST, 'sex');
        $firstname = filter_input(INPUT_POST, 'firstname');
        $lastname = filter_input(INPUT_POST, 'lastname');
        $address = filter_input(INPUT_POST, 'address');
        $city = filter_input(INPUT_POST, 'city');
        $zip = filter_input(INPUT_POST, 'zip-code');
        $phone = filter_input(INPUT_POST, 'tel');

        if (!$email) {
            $_SESSION['errors'][] = 'Invalid email address';
        }
        if (!User::isValidUsername(strtolower($_POST['username']))) {
            $_SESSION['errors'][] = 'The user name should begin with a Latin letter. It can then contain letters and numbers and must be 4 to 15 characters in length.';
        }
        if ($zip != '' && $zip != isInteger($zip)) {
            add_error('Zip code must be digit');
        }
        if (isset($sex) && ($sex != 'male' && $sex != 'female')) {
            add_error('Gender value is invalid');
        }

        if ($_POST['pass1'] != '' || $_POST['pass2'] != '') {
            if ($_POST['pass1'] != $_POST['pass2']) {
                $_SESSION['errors'][] = 'Passwords do not match.';
            } elseif (!User::isValidPassword($_POST['pass1'])) {
                $_SESSION['errors'][] = 'The password must be at least 6 characters long and must contain at least one lowercase, one capital letter and one digit. Letters must be Latin only.';
            }
        }

        $db = DB::getDB();

        if (Auth::user()->email !== $_POST['email']) {
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$_POST['email']]);
            if (count($stmt->fetchAll()) !== 0) {
                $_SESSION['errors'][] = 'There is already a user with this email address';
            }
        }

        if (Auth::user()->username !== $_POST['username']) {
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$_POST['username']]);
            if (count($stmt->fetchAll()) !== 0) {
                $_SESSION['errors'][] = 'There is already a user with this name';
            }
        }

        if (count($_SESSION['errors']) === 0) {
            $user = User::find(Auth::user()->id);
            if ($_POST['pass1'] != '') {
                $crypt_password = password_hash($_POST['pass1'], PASSWORD_BCRYPT);
                $user->password = $crypt_password;
            }

            $user->email = $email;
            $user->username = $_POST['username'];
            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->sex = $sex;
            $user->address = $address;
            $user->city = $city;
            $user->zip = $zip;
            $user->phone = $phone;
            $user->update();

            add_message('The data was successfully updated');
        }
        view('edit-profile');
    }

    public function indexAdmin()
    {
        $sex_values = get_enum_values('users', 'sex');
        $roles = get_enum_values('users', 'role');
        $statuses = get_enum_values('users', 'status');
        return view('admin.list-users', [
            'users' => User::all(),
            'sex' => $sex_values,
            'roles' => $roles,
            'statuses' => $statuses,
        ]);
    }

    public function editAdmin($id)
    {
        $user = User::find($id);
        $sex_values = get_enum_values('users', 'sex');
        $roles = get_enum_values('users', 'role');
        $statuses = get_enum_values('users', 'status');
        return view('admin.edit-user', [
            'user' => $user,
            'sex' => $sex_values,
            'roles' => $roles,
            'statuses' => $statuses,
        ]);
    }

    public function updateAdmin($id)
    {
        $user=User::find($id);

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $username = filter_input(INPUT_POST, 'username');
        $password = filter_input(INPUT_POST, 'password');
        $password_confirm = filter_input(INPUT_POST, 'password-confirm');

//        my_var_dump($password);

        $email_=User::query("SELECT id FROM users WHERE email='$email' AND id<>$id");
        $username_=User::query("SELECT id FROM users WHERE username='$username' AND id<>$id");
        if(!$email){
            add_error('The field for email is invalid');
        }elseif(count($email_)>0) {
           add_error('Another user use email ' . $email);
        }

        if(!$username){
            add_error('The field for username is invalid');
        }elseif(count($username_)>0) {
           add_error('This username is already taken');
        }

        if($password || $password_confirm){
            if(!User::isValidPassword($password)) {
                add_error('The passwords must contain capital letter');
            }elseif($password!=$password_confirm){
                add_error('The passwords do not match');
            }
        }

        if(isset($_POST['zip'])){
            if($_POST['zip']!='' && !isInteger($_POST['zip'])) {
                add_error('Invalid zip code');
            }else{
                $user->zip=$_POST['zip'];
            }
        }

        if(!haveErrors()) {
            $user->email = $email;
            $user->username = $username;
            if($password!=''){
                $user->password=password_hash($password, PASSWORD_BCRYPT);
            }
            $user->sex=$_POST['sex'];
            $user->role=$_POST['role'];
            $user->status=$_POST['status'];
            if(postHave('firstname')){
                $user->firstname=$_POST['firstname'];
            }
            if(postHave('lastname')){
                $user->lastname=$_POST['lastname'];
            }
            if(postHave('address')){
                $user->address=$_POST['address'];
            }
            if(postHave('city')){
                $user->city=$_POST['city'];
            }
            if(postHave('phone')){
                $user->phone=$_POST['phone'];
            }

            if($user->update()){
                add_message('User was updated successfully');
            }else{
                add_error('Error');
            }

        }

            redirect_back();
        }

        public function destroyAdmin($id)
        {
            User::query1("DELETE FROM users WHERE id=$id");
            add_message("User was successfully deleted");
            redirect_back();
        }
}