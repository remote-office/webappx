<?php

  namespace WebAppX\Traits;

  use WebAppX\Interfaces\Request;
  use WebAppX\Interfaces\Response;

  use LibX\Util\Stack;

  use RuntimeException;


  trait Middleware
  {
    /********************************************************************************
     * Middleware methods
     *******************************************************************************/

   	protected $middlewareStack;
   	protected $middlewareLock = false;

   	/**
   	 * Init middleware stack
   	 *
   	 * @param Callable $kernel
   	 * @return void
   	 * @throws RuntimeException
   	 */
  	protected function initMiddlewareStack($kernel = null)
   	{
  	  if(!is_null($this->middlewareStack))
      	throw new RuntimeException('MiddlewareStack can only be seeded once.');

      if(is_null($kernel))
      	$kernel = $this;

   		$this->middlewareStack = new Stack();
   		$this->middlewareStack[] = $kernel;
   	}

   	/**
   	 * Add middleware
   	 *
   	 * @param Callable $callable
   	 * @return App
   	 */
   	protected function addMiddleware(Callable $callable)
   	{
   		// Init stack
   		if(is_null($this->middlewareStack))
   			$this->initMiddlewareStack();

   		$next = $this->middlewareStack->first();

   		// Add callable to the stack
  		$this->middlewareStack[] = function(Request $request, Response $response) use ($callable, $next)
      {
  			$result = call_user_func($callable, $request, $response, $next);

  			/*if($result instanceof ResponseInterface === false)
        	throw new UnexpectedValueException('Middleware must return instance of \Psr\Http\Message\ResponseInterface');*/

        return $result;
  		};

      return $this;
   	}

   	/**
   	 * Call middleware
   	 *
   	 * @param RequestInterface $request
   	 * @param ResponseInterface $response
   	 * @return ResponseInterface
   	 */
   	protected function callMiddlewareStack(Request $request, Response $response)
   	{
   		if(is_null($this->middlewareStack))
   			$this->initMiddlewareStack();

   		/** @var callable $callable */
      $callable = $this->middlewareStack->first();

      // Lock middleware
      $this->middlewareLock = true;

      $response = $callable($request, $response);

      // Unlock middleware
      $this->middlewareLock = false;

   		return $response;
   	}

  }

?>