<?php


namespace Controllers;

use Pages\Route;

class HomeController
{
    #[Route(path: "/", name: "home")]
    public function home() : void {

    }

    #[Route(path: "/test/{test}", name: "test")]
    public function test() : void {

    }

    #[Route(path: "/test/slug/{test}", name: "test")]
    public function test3() : void {

    }
}
