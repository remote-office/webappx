<?php

	namespace WebAppX;

	use WebAppX\Interfaces\Request;
	use WebAppX\Interfaces\Response;

	class Route extends Routable
	{
		use Traits\Middleware;

		protected $methods;
		protected $groups;
		protected $arguments;

		public function __construct($methods, $pattern, $callable, $groups = [])
    {
			$this->methods  = is_string($methods) ? [$methods] : $methods;
			$this->pattern  = $pattern;
			$this->callable = $callable;

			$this->groups = $groups;
    }

    public function getMethods()
    {
    	return $this->methods;
    }

    public function getGroups()
    {
    	return $this->groups;
    }

    public function setArguments($arguments)
    {
    	$this->arguments = $arguments;
    }

    public function __invoke(Request $request, Response $response)
    {
      $this->callable = $this->resolve($this->callable);

    	// Route arguments
    	$arguments = [];

    	return call_user_func($this->callable, $request, $response, $this->arguments);
    }

	  /**
     * Run route
     *
     * This method traverses the middleware stack, including the route's callable
     * and captures the resultant HTTP response object. It then sends the response
     * back to the Application.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function run(Request $request, Response $response)
    {
    	// Collect middleware from groups
    	$groupMiddleware = [];
    	foreach($this->getGroups() as $group)
    		$groupMiddleware = array_merge($group->getMiddleware(), $groupMiddleware);

    	// Merge with middleware of route
      $this->middleware = array_merge($this->middleware, $groupMiddleware);

      // Add merged middleware to the middleware stack
      foreach($this->getMiddleware() as $middleware)
      	$this->addMiddleware($middleware);

     	// Traverse middleware stack and fetch updated response
      return $this->callMiddlewareStack($request, $response);
    }
	}

?>