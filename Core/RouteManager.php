<?php


namespace Core;


use JsonException;
use Pages\Route;
use ReflectionClass;
use ReflectionException;
// todo check for duplicates (name and query)
class RouteManager
{
    private string $routesCache = CONFIG['STORAGE'].'/routes.json';

    public function getRoutes() : array {

        $controllerPath = CONFIG['PATH'].'/Controllers';
        $controllers = scandir($controllerPath);
        $pages = array();

        foreach ($controllers as $controller) {
            if (str_contains($controller, ".php")) {
                $controller = str_replace(".php", "", "Controllers\\".$controller);

                $controllerArray = array("controller_name" => $controller);

                try {
                    $reflection = new ReflectionClass(new $controller);

                    $methods = $reflection->getMethods();

                    // iterate over each method and check if its a page
                    foreach ($methods as $method) {
                        $attributes = $method->getAttributes();

                        $methodArray = array("method_name" => $method->getName());

                        foreach ($attributes as $attribute) {
                            if ($attribute->getName() === Route::class) {
                                $methodArray["page"]= $attribute->getArguments();
                            }
                        }

                        $controllerArray["methods"][] = $methodArray;
                    }
                    // add methods if we have any
                    if (isset($controllerArray["methods"])) {
                        $pages[] = $controllerArray;
                    }

                } catch (ReflectionException $e) {
                    // todo handle exceptions
                }
            }
        }

        return $pages;
    }

    public function cacheRoutes() : void {
        $fp = fopen($this->routesCache, 'wb');
        try {
            fwrite($fp, json_encode($this->getRoutes(), JSON_THROW_ON_ERROR));
        } catch (JsonException $e) {
            // todo handle errors
        }
        fclose($fp);
    }

    public function readRoutes() : ?array {
        $string = file_get_contents($this->routesCache);
        try {
            return json_decode($string, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            // todo handle errors
            return null;
        }
    }
}
