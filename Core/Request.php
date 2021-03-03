<?php
namespace Core;

class Request
{
    private array $post;
    private array $get;

    /**
     * Request constructor.
     * @param Core $core
     */
    public function __construct(private Core $core)
    {
    }

    public function handle(array $post, array $get) : void {
        $routeManager= new RouteManager();
        $routes = $routeManager->getRoutes();
        $finalMethod = null;

        $urlPieces = array_filter((explode("/", $get['query'])));
        $urlPiecesNo = count($urlPieces);

        foreach ($routes as $route) {
            foreach ($route["methods"] as $method) {

                $pagePieces = explode("/",  $method["page"]["path"]);
                $pagePieces = array_values(array_filter($pagePieces));

                if (empty($pagePieces[0])) {
                    $pagePieces = array("/");
                }

                if (empty($urlPieces[0])) {
                    $urlPieces = array("/");
                }

                $pagePiecesNo = count($pagePieces);

                // skip all routes that are shorter than the url
                if ($urlPiecesNo > $pagePiecesNo) {
                    continue;
                }

                // if the first part of the query does not match we skip other checks
                if ($urlPieces[0] !== $pagePieces[0]) {
                    continue;
                }

                for ($i = 1; $i < $urlPiecesNo; $i++) {
                    // we found a match, now check if it's perfect
                    // Check if the query item matches or if it's a slug, if none, we don't check further
                    if ($urlPieces[$i] === $pagePieces[$i]) {
                        $finalMethod = $method;
                    } elseif (str_starts_with($pagePieces[$i],"{") &&
                        str_ends_with($pagePieces[$i],"}")) {
                        $finalMethod = $method;
                    } else {
                        break;
                    }
                }

                if ($finalMethod === null) {
                   throw new \Exception("404 not found");
                }
            }
        }

        var_dump($finalMethod);
    }
}
