<?php

  namespace WebAppX;

  use ArrayAccess;

  /**
   * Class Container
   *
   * @author David Betgen <d.betgen@remote-office.nl>
   * @version 1.0
   */
  class Container implements ArrayAccess, Interfaces\Container
  {
    // Array access
    private $values = array();
    private $keys = array();

    private $raw = array();

    /**
     * Construct a Container
     *
     * @param array $settings
     * @return Container
     */
    public function __construct(array $config = [])
    {
      // Register config
      foreach($config as $id => $value)
        $this->offsetSet($id, $value);

      // Check if settings are present in config
      $settings = isset($config['settings']) ? $config['settings'] : [];

      // Register settings (overwrite)
      $this['settings'] = function() use ($settings)
      {
        return new Collection($settings);
      };

      // Register service provider
      $serviceprovider = new ServiceProvider();
      $serviceprovider->register($this);
    }

    public function get($id)
    {
      return $this->offsetGet($id);
    }

    public function has($id)
    {
      return $this->offsetExists($id);
    }

    /**
     *
     * {@inheritDoc}
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($id)
    {
      if(!isset($this->keys[$id]))
        //throw new UnknownIdentifierException($id);
        throw new \Exception($id);

      // Get value from array
      $value = $this->values[$id];

      if(!is_object($value))
        return $value;

      // Values is object (Closure)
      if($value instanceof \Closure)
      {
        // Get function
        $function = $this->values[$id];

        // Execute function
        $value = $function($this);

        $this->values[$id] = $value;
      }

      return $value;
    }

    /**
     *
     * {@inheritDoc}
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($id)
    {
      return isset($this->keys[$id]);
    }

    /**
     *
     * {@inheritDoc}
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($id)
    {
      if(isset($this->keys[$id]))
      {
        if(is_object($this->values[$id]))
        {

        }

        unset($this->keys[$id], $this->values[$id]);
      }
    }

    /**
     *
     * {@inheritDoc}
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($id, $value)
    {
      $this->values[$id] = $value;
      $this->keys[$id] = true;
    }

    /**
     * Magic methods for convenience
     */
    public function __isset($name)
    {
      return $this->has($name);
    }

    public function __get($name)
    {
      return $this->get($name);
    }
  }

?>