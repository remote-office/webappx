<?php

	namespace WebAppX;

	use WebAppX\Interfaces\Container;

  use Closure;
  use RuntimeException;

	abstract class Routable
	{
		protected $container;

		protected $name;

		protected $pattern;
		protected $callable;
		protected $middleware = [];

		public function setContainer(Container $container)
		{
			$this->container = $container;
		}

		public function getContainer()
		{
			return $this->container;
		}

		public function getName()
		{
			return $this->name;
		}

		public function setName($name)
		{
			$this->name = $name;
		}


		public function getPattern()
    {
    	return $this->pattern;
    }

    public function getCallable()
    {
    	return $this->callable;
    }

    public function getMiddleware()
    {
    	return $this->middleware;
    }

    /**
     * Prepend middleware to the middleware collection
     *
     * @param callable|string $callable The callback routine
     *
     * @return static
     */
		public function add($callable)
    {
    	$this->middleware[] = $callable;
      //return $this->addMiddleware(new DeferredCallable($callable, $this->container));
      //return $this->addMiddleware($callable);
    }

    /**
     * Resolve a string of the format 'class:method' into a closure that the
     * router can dispatch.
     *
     * @param callable|string $callable
     *
     * @return Closure
     *
     * @throws RuntimeException If the string cannot be resolved as a callable
     */
    protected function resolve($callable)
    {
			if(!$this->container instanceof Container)
				return $callable;

      $resolver = $this->container->get('resolver');

      return $resolver->resolve($callable);
    }
	}

?>