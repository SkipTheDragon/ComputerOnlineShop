<?php
namespace Core;

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

    /**
     * Router constructor.
     * @param string $url
     */
    public function __construct(private string $url)
    {
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

        $urlPieces = array_filter((explode("/", $this->url)));
        $urlPiecesNo = count($urlPieces);

        $ranks = array();

        /** @var Route $route */
        foreach ($routes as $route) {
            $pagePieces = explode("/", $route->getPath());
            $pagePieces = array_values(array_filter($pagePieces));

            $slugs = array();
            $rank = array();

            if (empty($pagePieces)) {
                $pagePieces = array("/");
            }

            if (empty($urlPieces)) { // if url is empty search for homepage
                $urlPieces = array("/");

                if ($pagePieces[0] === $urlPieces[0]) {
                    $this->currentRoute = $route;
                    return;
                }
            }

            $pagePiecesNo = count($pagePieces);

            // skip all routes that are shorter than the url
            if ($urlPiecesNo > $pagePiecesNo) {
                $ranks[] = 0; // add a default value to route rank so we can keep it the same size as routes array
                continue;
            }
            // if the first part of the query does not match we skip other checks
            if ($urlPieces[0] !== $pagePieces[0]) {
                continue;
            }

            for ($i = 0; $i < $urlPiecesNo; $i++) {

                if ($urlPieces[$i] === $pagePieces[$i]) {
                    $rank[] = 10;
                } elseif (str_starts_with($pagePieces[$i],"{") && str_ends_with($pagePieces[$i],"}")) {
                    $slugs[] = array($pagePieces[$i] => $urlPieces[$i]);
                    $rank[] = 1;
                }

            }

            $ranks[] = array_sum($rank);

            if (count($ranks) === $pagePiecesNo) {
                $route->setSlugs($slugs);
            }
        }
        $max_keys = array_keys($ranks, max([max($ranks)]));

        $this->currentRoute = $routes[$max_keys[0]];
    }


}
