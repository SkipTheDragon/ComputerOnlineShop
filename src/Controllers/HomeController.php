<?php


namespace Controllers;


use Core\Routes\Route;

class HomeController
{
    #[Route(path: "/", name: "home")]
    public function home() : void {
        echo "home";
    }

    #[Route(path: "/test/{test}/{test2}", name: "test_two_slugs")]
    public function test1() : void {
        echo "test1";

    }

    #[Route(path: "/test/slug/{test}", name: "test_one_slug_one_constant")]
    public function test2() : void {
        echo "test2";

    }

    #[Route(path: "/test/test-{test}", name: "test_slug_with_constant")]
    public function test3() : void {
        echo "test3";

    }

    #[Route(path: "/test/{test1}-{test2}", name: "test_double_slug")]
    public function test4() : void {
        echo "test4";
    }

    #[Route(path: "/test/{test1}-{test2}-{test3}", name: "test_triple_slug")]
    public function test5() : void {
        echo "test5";
    }
}
