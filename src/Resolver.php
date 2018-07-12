<?php

	namespace WebAppX;

	use WebAppX\Interfaces\Container

	final class Resolver
	{
		const CALLABLE_PATTERN = '!^([^\:]+)\:([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$!';

		protected $container;

		public function __construct(Container $container)
		{
			$this->container = $container;
		}

		public function resolve($resolve)
		{
		  $callable = null;

			if(!is_callable($resolve))
			{
				if(is_string($resolve))
				{
					// Check for callable as "class:method"
    			if(preg_match(self::CALLABLE_PATTERN, $resolve, $matches))
      			$callable = $this->convert($matches[1], $matches[2]);
     			else
     				$callable = $this->convert($resolve);
				}
      }
      else
      {
      	$callable = $resolve;
      }

      if(!is_callable($callable))
				throw new \RuntimeException(sprintf('%s is not resolvable', is_array($callable) || is_object($callable) ? json_encode($callable) : $callable));

			return $callable;
		}

		/**
     * Check if string is something in the DIC
     * that's callable or is a class name which has an __invoke() method.
     *
     * @param string $class
     * @param string $method
     * @return callable
     *
     * @throws \RuntimeException if the callable does not exist
     */
    protected function convert($class, $method = '__invoke')
    {
    	if($this->container->has($class))
      	return [$this->container->get($class), $method];

     	if(!class_exists($class))
     		throw new \RuntimeException(sprintf('Callable %s does not exist', $class));

     	return [new $class($this->container), $method];
    }
	}

?>