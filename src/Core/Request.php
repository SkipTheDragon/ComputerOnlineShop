<?php
namespace Core;

class Request
{
    /**
     * @var Router
     */
    private Router $router;

    /**
     * Request constructor.
     * @param Core $core
     * @param array $post
     * @param array $get
     */
    public function __construct(private Core $core, private array $post, private array $get)
    {
        $this->router = new Router($get["query"]);

        $this->initController();
    }

    public function initController() : void {
        $route = $this->router->getCurrentRoute();
        $className = $route->getClassName();
        $methodName = $route->getMethodName();
        $class = new $className;
        $class->{$methodName}();
    }

    private function mapQueryDataToMethod() : void {

    }
}
