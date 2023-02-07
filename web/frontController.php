<?php
namespace App\web;
use App\Controller ;
use App\Controller\ControllerAdmin;
use App\Controller\ControllerUser;
use App\Controller\GenericController;
use App\Lib\Psr4AutoloaderClass;

require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';

// instantiate the loader
$loader = new Psr4AutoloaderClass();
// register the base directories for the namespace prefix
$loader->addNamespace('App', __DIR__ . '/../src');
// register the autoloader
$loader->register();


if(isset($_GET['controller'])) //si le controller est indiqué dans l'URL
{
    $controller = ucfirst($_GET['controller']); //usfirst met le premier caractere en majuscule
    $controllerClassName = "App\Controller\Controller" . $controller;

    if (class_exists($controllerClassName)) { //on vérifie si la class controller existe
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
            if(method_exists($controllerClassName,$action)) //on vérifie la method action
            {
                $controllerClassName::$action();
            }
            else{
                GenericController::error();
            }
        } else {
            $action = "readAll";
            $controllerClassName::$action();
        }
    } else {                                //sinon on renvoit une erreur
        GenericController::error();
    }

}
else //si il n'y a pas de controller dans l'URL
{
    $controller = "User"; //le controlleur "user" est choisi par défaut
    $controllerClassName = "App\Controller\Controller" . $controller;

    if(isset($_GET['action'])) {
        $action = $_GET['action'];
        if(method_exists($controllerClassName,$action))
        {
            $controllerClassName::$action();
        }
        else{
            GenericController::error();
        }
    }
    else{
        $action="accueil";
        $controllerClassName::$action();
    }
}


