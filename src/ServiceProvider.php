<?php

	namespace WebAppX;
	
  class ServiceProvider
  {
    public function register($container)
    {
      if(!isset($container['environment']))
      {
        /**
         * @return Environment
         */
        $container['environment'] = function($container)
        {
          return new Environment($_SERVER);
        };
      }
      
      if(!isset($container['request']))
      {
      	$container['request'] = function($container)
        {
      		return Request::createFromEnvironment($container->get('environment'));	
        };
      }
      
      if(!isset($container['response']))
      {
      	$container['response'] = function($container)
        {
      		return new Response();
        };
      }
      
    	if(!isset($container['router']))
      {
        /** 
         * 
         * @return RouterInterface
         */
        $container['router'] = function($container)
        {
          $router = new Router();
          $router->setContainer($container);
          
          return $router;
        };
      }
      
    	if(!isset($container['resolver']))
      {
        /**
         * 
         * @return ResolverInterface
         */
        $container['resolver'] = function($container)
        {
          return new Resolver($container);
        };
      }
    }
  }

?>