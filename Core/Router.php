<?php

namespace Core;


use Core\Error;
/**
 * Router
 *
 * PHP version 5.4
 */
class Router
{

    /**
     * Associative array of routes (the routing table)
     * @var array
     */
    protected $routes = [];

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $params = [];

    protected static $passthru = [
        'get', 'post', 'put', 'patch', 'delete', 'options', 'any',
    ];

    protected $methods = [];
    protected $controllers = [];
    protected $actions = [];
    protected $name = [];
    protected $route;


    private function add($route, $method, $controller, $action)
    {
        // Convert the route to a regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);

        // Convert variables e.g. {controller}
        $route = preg_replace('/\{([a-z0-9]+)\}/', '(?P<\1>[a-z0-9-]+)', $route);

        // Add start and end delimiters, and case insensitive flag
        $route = '/^' . $route . '\/?$/iu';
//        $route = '/^' . $route . '$/iu';

        $this->routes[] = $route;
        $this->controllers[] = $controller;
        $this->actions[] = $action;
        $this->methods[] = $method;
        return $this;
    }


    public function getRoutes()
    {
        return $this->routes;
    }


    public function method_match ($route)
    {
        if (array_key_exists($route, $this->methods)) {
            foreach ($this->methods as $key=>$index){
                if($key === $route) {
                    if ($_SERVER['REQUEST_METHOD'] === $index) {
                        return true;
                    }
                }
            }
        }
    }


    public function match($url)
    {
        $server_method = '';
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){
            $server_method = 'GET';
        }elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['_method']))
                $server_method = $_POST['_method'];
            else $server_method = 'POST';
        }else{
            die();
        }

        foreach ($this->routes as $route => $params) {
            if (preg_match($params, $url, $matches) && $server_method === $this->methods[$route]) {
                $p = [];
                $this->route = $route;
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $p[$key] = $match;
                    }
                }
                $this->params = $p;
                return true;
            }
        }
        return false;
    }

    public function __call($method, $parameters)
    {
        $temp = explode('@', $parameters[1]);
        if(count($temp) != 2 || $temp[0]==='' || $temp[1]==='' )
            throw new \Exception('Липсващи параметри в шаблона "контролер@функция"');
        $controller = $temp[0];
        $action = $temp[1];

        if (in_array($method, self::$passthru)) {
//            echo $method;
            $method = strtoupper($method);
            return $this->add($parameters[0], $method, $controller, $action);
        }
    }

    protected function getController()
    {
        return explode('@', $this->routes[$this->route])[0];
    }

    public function dispatch($url)
    {
        $url = $this->removeQueryStringVariables($url);
        if ($this->match($url)) {
            $controller = $this->controllers[$this->route];
            $controller = $this->getNamespace() . $controller;
            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);

                $action = $this->actions[$this->route];
                $action = $this->convertToCamelCase($action);

                if (is_callable([$controller_object, $action])) {
                    $a=[];
                    foreach ($this->params as $value){
                        $a[]=$value;
                    }
                    $controller_object->$action(...$a);
                } else {
                    throw new \Exception("Method $action (in controller $controller) not found");
                }
            } else {
                throw new \Exception("Controller class $controller not found");
            }
        } else {
            throw new \Exception('There is no such page', 404);
        }
//        }
//        catch (\Exception $e){
//            add_error($e->getMessage());
//            View::renderTemplate('Posts/index.html', [
//                'category' => 'Грешка',
//                'real_category_name' => 'Грешка',
//                'data' => Data::getSessionData()
//            ]);
//        }
    }


    protected function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    protected function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    protected function removeQueryStringVariables($url)
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }


    protected function getNamespace()
    {
        $namespace = 'App\Controllers\\';
        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }

        return $namespace;
    }

}
