<?php

	namespace WebAppX;

	/**
	 * Class RouteGroup
	 *
	 * A collector for Routable objects with a common middleware stack
	 *
	 * @author David Betgen <d.betgen@remote-office.nl>
	 * @version 1.o
	 */
	class RouteGroup extends Routable
	{
    /**
     * Create a new RouteGroup
     *
     * @param string   $pattern  The pattern prefix for the group
     * @param callable $callable The group callable
     */
    public function __construct($pattern, $callable)
    {
    	$this->pattern = $pattern;
      $this->callable = $callable;
    }

    /**
     * Invoke the group to register any Routable objects within it.
     *
     * @param App $app The App to bind the callable to.
     */
    public function __invoke(App $app = null)
    {
      $callable = $this->resolve($this->callable);

      // Use of $this in closure refers to app
      if($callable instanceof \Closure && $app !== null)
      	$callable = $callable->bindTo($app);

      $callable();
    }
	}