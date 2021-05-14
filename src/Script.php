<?php

  namespace WebAppX;
  
  use WebAppX\Interfaces\Container;
  use WebAppX\Interfaces\Request;
  use WebAppX\Interfaces\Response;
  
  use Closure;
  use Exception;
  use InvalidArgumentException;
  
  /**
   * Class Script
   *
   * @author David Betgen <d.betgen@remote-office.nl>
   * @version 1.0
   */
  class Script
  {
    private $container;
    
    /**
     * Construct a Script
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
  }
  
?>