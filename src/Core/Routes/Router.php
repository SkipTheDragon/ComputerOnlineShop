<?php
namespace Core\Routes;

use JsonException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

// todo check for duplicates (name and query)

class Router
{
    private string $routesCacheLocation = CONFIG["PATHS"]["STORAGE"].'/routes.json';
    private Route $currentRoute;
    private string $url;

    /**
     * Router constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = "/".$url;

        $this->guessRoute();
    }

    /**
     * @return Route
     */
    public function getCurrentRoute(): Route
    {
        if (isset($this->currentRoute) ) {
            return $this->currentRoute;
        }

        throw new RuntimeException("404 No route found");
    }

    private function getAllRoutes() : array
    {
        $controllerPath = CONFIG["PATHS"]["CODE"].'/Controllers';
        $controllers = scandir($controllerPath);
        $routes = array();

        foreach ($controllers as $controller) {
            if (str_contains($controller, ".php")) {
                $controller = str_replace(".php", "", "Controllers\\".$controller);

                $routes[] = $this->getRoutesFromClass($controller);
            }
        }

        return array_merge(...$routes);
    }

    private function getRoutesFromClass(string $class) : array {
        $controllers = array();

        try {
            $reflection = new ReflectionClass($class);
            $methods = $reflection->getMethods();
            foreach ($methods as $method) {
                $attributes = $method->getAttributes(Route::class, ReflectionAttribute::IS_INSTANCEOF);

                foreach ($attributes as $attribute) {
                    /** @var Route $route */
                    $route = $attribute->newInstance();
                    $route->setMethodName($method->getName());
                    $route->setClassName($class);
                    $controllers[] = $route;
                }
            }
        } catch (ReflectionException $e) {
            // todo add error management
        }

        return $controllers;
    }

    private function cacheRoutes() : void
    {
        $fp = fopen($this->routesCacheLocation, 'wb');
        try {
            fwrite($fp, json_encode($this->getAllRoutes(), JSON_THROW_ON_ERROR));
        } catch (JsonException $e) {
            // todo handle errors
        }
        fclose($fp);
    }

    private function readRoutes() : ?array
    {
        $string = file_get_contents($this->routesCacheLocation);
        try {
            return json_decode($string, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            // todo handle errors
            return null;
        }
    }

    private function guessRoute() : void {
        $routes = $this->getAllRoutes();
        $matchingRoutes = [];

        /** @var Route $route */
        foreach ($routes as $route) {
            if ($route->match($this->url)) {
                $matchingRoutes[] = $route;
            }
        }
        if (count($matchingRoutes) > 1) {
            usort($matchingRoutes, static function ($route1, $route2) {

                if ($route1->getConstantsNo() > $route2->getConstantsNo() &&
                    $route1->getMatches() < $route2->getMatches() ) {
                    return -1;
                }

                return 1;
            });

        }

        $this->currentRoute = $matchingRoutes[0];
    }


}
