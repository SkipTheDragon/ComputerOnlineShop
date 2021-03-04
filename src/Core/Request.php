<?php
namespace Core;

use Core\Routes\Router;

class Request
{
    /**
     * @var Router
     */
    private Router $router;

    private string $routeName;

    /**
     * Request constructor.
     * @param Core $core
     * @param array $post
     * @param array $get
     */
    public function __construct(private Core $core, private array $post, private array $get)
    {

    }

    public function route() : void {
        $this->router = new Router($this->get["query"]);
        $route = $this->router->getCurrentRoute();

        $this->routeName = $route->getName();

        $className = $route->getClassName();
        $methodName = $route->getMethodName();
        $class = new $className;
        $class->{$methodName}();
    }


    private function mapQueryDataToMethod() : void {

    }

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }


}
