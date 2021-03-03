<?php


namespace Controllers;


use Core\Route;

class HomeController
{
    #[Route(path: "/", name: "home")]
    public function home() : void {
        echo "home";
    }

    #[Route(path: "/test/{test}/{test2}", name: "test")]
    public function test1($test, $test2) : void {
        echo "test1";

    }

    #[Route(path: "/test/slug/{test}", name: "test")]
    public function test2() : void {
        echo "test2";

    }

    #[Route(path: "/test/test-{test}", name: "test")]
    public function test3() : void {
        echo "test3";

    }
}
