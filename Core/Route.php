<?php
namespace Pages;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]

class Route {

    /**
     * Page constructor.
     * @param string $path
     * @param string $name
     */
    public function __construct(public string $path, public string $name)
    {

    }


}
