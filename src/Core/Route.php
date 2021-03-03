<?php
namespace Core;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]

class Route
{
    private string $methodName;
    private string $className;
    private array $slugs;

    /**
     * Page constructor.
     * @param string $path
     * @param string $name
     */
    public function __construct(private string $path, private string $name)
    {

    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * @param string $methodName
     */
    public function setMethodName(string $methodName): void
    {
        $this->methodName = $methodName;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    /**
     * @return array
     */
    public function getSlugs(): array
    {
        return $this->slugs;
    }

    /**
     * @param array $slugs
     */
    public function setSlugs(array $slugs): void
    {
        $this->slugs = $slugs;
    }


}
