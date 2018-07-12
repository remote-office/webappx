<?php

	namespace WebAppX\Views;

  use WebAppX\Interfaces\Response;

	class Twig implements \ArrayAccess
	{
    /**
     * Twig loader
     *
     * @var \Twig_LoaderInterface
     */
    protected $loader;

		/**
     * Twig environment
     *
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * View variables
     *
     * @var array
     */
		protected $variables = [];


		/********************************************************************************
     * Constructors and service provider registration
     *******************************************************************************/

    /**
     * Create new Twig view
     *
     * @param string|array $path     Path(s) to templates directory
     * @param array        $settings Twig environment settings
     */
		public function __construct($path, $settings = [])
    {
    	$this->loader = $this->createLoader(is_string($path) ? [$path] : $path);
      $this->environment = new \Twig_Environment($this->loader, $settings);
    }

    /********************************************************************************
     * Methods
     *******************************************************************************/

		/**
     * Fetch the content of a template
     *
     * @param string $template
     * @param array $data
     * @param string $cacheId
     * @return void
     */
    public function fetch($template, $data = [], $cacheId = null)
    {
      $data = array_merge($this->variables, $data);

      return $this->environment->render($template, $data);
    }

	  /**
     * Output rendered template
     *
     * @param ResponseInterface $response
     * @param  string $template Template pathname relative to templates directory
     * @param  array $data Associative array of template variables
     * @return ResponseInterface
     */
    public function render(Response $response, $template, $data = [])
    {
      $response->getBody()->write($this->fetch($template, $data));

      return $response;
    }

		/**
     * Create a loader with the given path
     *
     * @param array $paths
     * @return \Twig_Loader_Filesystem
     */
    private function createLoader(array $paths)
    {
      $loader = new \Twig_Loader_Filesystem();

      foreach($paths as $namespace => $path)
      {
      	if(is_string($namespace))
        	$loader->setPaths($path, $namespace);
        else
        	$loader->addPath($path);
     	}

      return $loader;
    }


		/********************************************************************************
     * ArrayAccess interface
     *******************************************************************************/

    /**
     * Does this collection have a given key?
     *
     * @param  string $key The data key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
			return array_key_exists($key, $this->variables);
    }

    /**
     * Get collection item for key
     *
     * @param string $key The data key
     *
     * @return mixed The key's value, or the default value
     */
    public function offsetGet($key)
    {
      return $this->variables[$key];
    }

    /**
     * Set collection item
     *
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function offsetSet($key, $value)
    {
      $this->variables[$key] = $value;
    }

    /**
     * Remove item from collection
     *
     * @param string $key The data key
     */
    public function offsetUnset($key)
    {
    	unset($this->variables[$key]);
    }
	}

?>