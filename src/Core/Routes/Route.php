<?php
namespace Core\Routes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]

class Route
{
    private string $methodName;
    private string $className;
    private array $slugs;
    private int $matches;

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

    /**
     * @return int
     */
    public function getMatches(): int
    {
        return $this->matches;
    }

    public function hasSlug() : bool {
        return preg_match('#(?<={)(.+)(?=})#', $this->path) === 1;
    }

    public function getSlugsNo() : int {
        return preg_match_all('#{(.*?)}#', $this->path);
    }

    public function getConstantsNo() : int {
        $noSlugs = preg_replace('#-?{(.*?)}-?#', "", $this->path); // remove all slugs
        $noSlugs = preg_replace('#^/#', "", $noSlugs); // remove first char
        $noSlugs = preg_replace('#/$#', "", $noSlugs); // remove last char
        return preg_match_all("#(\w.*?/)#", $noSlugs);
    }

    // hybrid constant plus slug #/(.*?-.*?)/#, maybe not useful anymore
    public function match(string $url) : bool {
        $path = $this->getPath();
        if ($url === $path) {
            return true;
        }

        if ($this->hasSlug()) {

            $pattern = preg_replace("#{(.*?)}#","(\w+)", $path);
            $doesItMatch = preg_match_all("#".$pattern."#", $url, $matches2);
            $this->matches = count($matches2);
            if ($doesItMatch === 1) {
                return true;
            }

        }

        return false;
    }
}
