<?php

class Router
{
    private array $routes = [];
    private array $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function add($method, $path, $handler, $auth = false)
    {
        $this->routes[] = compact('method', 'path', 'handler', 'auth');
    }

    public function get($p, $h, $a=false){$this->add('GET',$p,$h,$a);}
    public function post($p, $h, $a=false){$this->add('POST',$p,$h,$a);}
    public function put($p, $h, $a=false){$this->add('PUT',$p,$h,$a);}
    public function delete($p, $h, $a=false){$this->add('DELETE',$p,$h,$a);}

    public function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // Get the script's directory (e.g., /REST_API_with_JWTAuthentication/public)
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        
        // If the script is in a subdirectory (like /public), go up one level to find the app root if necessary,
        // OR just strip the script's directory prefix from the URI.
        // Actually, since all requests are rewritten to public/index.php, the "base" for the API is likely the project root.
        // But for simplicity, let's just strip the common prefix.
        
        if (strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        } elseif (strpos($uri, dirname($scriptDir)) === 0) {
             $uri = substr($uri, strlen(dirname($scriptDir)));
        }

        $uri = '/' . ltrim($uri, '/');

        foreach ($this->routes as $route) {
            $pattern = "@^" . preg_replace('/\{[^}]+\}/', '([^/]+)', $route['path']) . "$@";
            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {

                array_shift($matches);

                if ($route['auth']) {
                    AuthMiddleware::handle($this->request);
                }

                [$class, $func] = $route['handler'];
                call_user_func_array([new $class, $func], $matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
}
