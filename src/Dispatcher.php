<?php

	namespace WebAppX; 

	/**
	 * Class Dispatcher
	 * 
	 * @author David Betgen <d.betgen@remote-office.nl>
	 * @version 1.0
	 */
	class Dispatcher
	{
	  protected $callback;
	  
	  public function __construct(Callable $callback)
	  {
	    $this->callback = $callback;
	  }
	  
		public function dispatch($method, $uri)
		{
      $routes = array();
		  
      // Init arguments
		  $arguments = array();
		  
      // Callback
			$callback = $this->callback;
			$callback($routes);
			
		  foreach($routes as $route)
      {
        $pattern = $route->getPattern();
        
        // Convert path to regex
        $regex = preg_replace('@:[\w]+@','([a-zA-Z0-9_\+\-%]+)', $pattern);
        $regex.= '/?(\?.*)?';
        
        $path = trim($_SERVER['REQUEST_URI']);
        
        // Check for a match
        if(preg_match('@^' . $regex . '$@', $path))
        {
        	// Match all :param as key
        	preg_match_all('@:([\w]+)@', $pattern, $keys, PREG_PATTERN_ORDER);
	        // Shift
	        array_shift($keys);
	        // Pop
	        $keys = array_pop($keys);
        
        	if(count($keys) > 0)
        	{
        		// Match values to keys
          	if(preg_match('@^' . $regex . '$@', $path, $matches))
         	 	{
	         	 	// Shift
	            array_shift($matches);
	
	            // Construct arguments
	            if(count($matches) > 0)
	            {
	              foreach($keys as $index => $key)
	                $arguments[$key] = $matches[$index];
	                
	              $route->setArguments($arguments);
	            }
          	}
        	}
        	
        	return $route;
        }
      }
		}
	}
	
?>