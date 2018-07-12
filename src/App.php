<?php

	namespace WebAppX;

	use WebAppX\Interfaces\Container;
	use WebAppX\Interfaces\Request;
	use WebAppX\Interfaces\Response;
	
  use Closure;
  use Exception;
  use InvalidArgumentException;

	/**
	 * Class App
	 *
	 * @author David Betgen <d.betgen@remote-office.nl>
	 * @version 1.0
	 */
  class App
  {
    use Traits\Middleware;

    private $container;

    /**
     * Construct an App
     *
     * @param Container $container
     * @throws InvalidArgumentException
     * @return App
     */
    public function __construct(Container $container)
    {
      if(!$container instanceof Interfaces\Container)
        throw new \InvalidArgumentException('Expected a ContainerInterface');

      $this->container = $container;
    }

    public function getContainer()
    {
      return $this->container;
    }

    public function get($pattern, $callable)
    {
    	return $this->map(['GET'], $pattern, $callable);
    }

    public function post($pattern, $callable)
    {
    	return $this->map(['POST'], $pattern, $callable);
    }

    public function any($pattern, $callable)
    {
    	return $this->map(['GET', 'POST'], $pattern, $callable);
    }

    public function map(array $methods, $pattern, $callable)
    {
      // Use of $this in closure refers to contaier
    	if($callable instanceof Closure)
      	$callable = $callable->bindTo($this->container);

     	$route = $this->container->get('router')->map($methods, $pattern, $callable);

     	/*if(is_callable([$route, 'setContainer']))
      	$route->setContainer($this->container);

      if(is_callable([$route, 'setOutputBuffering']))
      	$route->setOutputBuffering($this->container->get('settings')['outputBuffering']);*/

      return $route;
    }

    public function group($pattern, $callable)
    {
    	// Get router
    	$router = $this->container->get('router');

    	// Create RouteGroup from pattern and callable and push group onto the groups stack
    	$group = $router->pushGroup($pattern, $callable);
    	// Invoke group
    	$group($this);
    	// Pop group off the stack
    	$group = $router->popGroup();

    	return $group;
    }

    public function run()
    {
			$request = $this->container->get('request');
			$response = $this->container->get('response');

			try
			{
				// Process request
				$response = $this->process($request, $response);
			}
			catch(Exception $exception)
			{

			}

			if($response->getStatus() == 302)
			{
				$location = $response->getHeader('Location');

				header('Location: ' . $location);
				exit;
			}

			echo $response->getBody();
    }

    protected function process(Request $request, Response $response)
    {
    	// Call middleware
    	$response = $this->callMiddlewareStack($request, $response);

    	return $response;
   	}


   	public function __invoke(Request $request, Response $response)
   	{
   		$router = $this->container->get('router');

   		$route = $router->dispatch($request);

   		if(!is_null($route))
       return $route->run($request, $response);

      echo 'No routes!';

      return $response;
   	}

    public function add($callable)
    {
      //return $this->addMiddleware(new DeferredCallable($callable, $this->container));
      return $this->addMiddleware($callable);
    }
  }

?>