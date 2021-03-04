<?php

namespace Core;

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    public static function setUpBeforeClass() : void {
        $_SERVER['DOCUMENT_ROOT'] = "./";
        require 'config.php';
    }

    public function test_home(): void
    {
        $_GET["query"] = "";
        $request = new Request(new Core(), $_POST, $_GET);
        $request->route();

        self::assertSame($request->getRouteName(), "home");
    }

    public function test_two_slugs(): void
    {
        $_GET["query"] = "test/test/test2";
        $request = new Request(new Core(), $_POST, $_GET);
        $request->route();

        self::assertSame($request->getRouteName(), "test_two_slugs");
    }

    public function test_one_slug_one_constant(): void
    {
        $_GET["query"] = "test/slug/test2";
        $request = new Request(new Core(), $_POST, $_GET);
        $request->route();

        self::assertSame($request->getRouteName(), "test_one_slug_one_constant");
    }

    public function test_slug_with_constant(): void
    {
        $_GET["query"] = "test/test-69";
        $request = new Request(new Core(), $_POST, $_GET);
        $request->route();

        self::assertSame($request->getRouteName(), "test_slug_with_constant");
    }

    public function test_double_slug(): void
    {
        $_GET["query"] = "test/69-69";
        $request = new Request(new Core(), $_POST, $_GET);
        $request->route();

        self::assertSame($request->getRouteName(), "test_double_slug");
    }


    public function test_triple_slug(): void
    {
        $_GET["query"] = "test/69-670-70";
        $request = new Request(new Core(), $_POST, $_GET);
        $request->route();

        self::assertSame($request->getRouteName(), "test_triple_slug");
    }
}
