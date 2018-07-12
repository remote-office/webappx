<?php

	namespace WebAppX;

	use WebAppX\Interfaces\Container;
	use WebAppX\Interfaces\Request;
	use WebAppX\Interfaces\Response;

	class Router
	{
		protected $container;
		protected $groups = [];
		protected $routes = [];

		public function __construct()
		{

		}

		public function setContainer(Container $container)
		{
			$this->container = $container;
		}

		public function getContainer()
		{
			return $this->container;
		}

		public function map(array $methods, $pattern, $callable)
		{
		  // Prepend parent group pattern(s)
      if(!empty($this->groups))
      {
      	$pp = '';
      	foreach($this->groups as $group)
      	  $pp .= $group->getPattern();

     		$pattern = $pp . $pattern;
      }

			// Create a new route
			$route = new Route($methods, $pattern, $callable, $this->groups);
			$route->setContainer($this->container);

			// Add route to routes
			$this->routes[] = $route;

			return $route;
		}

		public function dispatch(Request $request)
		{
			// Get dispatcher
			$dispatcher = $this->createDispatcher();

			// Extract uri
			$uri = trim($_SERVER['REQUEST_URI']); //$request->getUri()->getPath()

			$route = $dispatcher->dispatch($request->getMethod(), $uri);

			return $route;
		}


		protected function createDispatcher()
		{
      $callback = function (array &$routes)
      {
        //foreach($this->getRoutes() as $route)
        foreach($this->routes as $route)
        {
          $routes[] = $route;
          //$r->addRoute($route->getMethods(), $route->getPattern(), $route->getIdentifier());
        }
      };

      return new Dispatcher($callback);
		}

		public function pushGroup($pattern, $callable)
		{
			$group = new RouteGroup($pattern, $callable);
			$group->setContainer($this->container);

      array_push($this->groups, $group);

      return $group;
		}

		public function popGroup()
		{
			$group = array_pop($this->groups);

      return $group instanceof RouteGroup ? $group : false;
		}

	}

?>