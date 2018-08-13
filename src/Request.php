<?php

	namespace WebAppX;

	use WebAppX\Http\Uri\UriInterface;
	use WebAppX\Http\Uri\Uri;

	use LibX\Stream\Stream;

	class Request extends Message implements Interfaces\Request
	{
		protected $method;
		protected $uri;
		protected $headers;
		protected $cookies;
		protected $parameters;
		protected $body;
		protected $files;

		protected $attributes;

		// Status
    protected $dispatched;

    /**
     * Construct a Request
     *
     * @param string $method
     * @param UriInterface $uri
     * @param array $headers
     * @param array $cookies
     * @param array $parameters
     * @param string $body
     * @param array $files
     * @return Request
     */
		public function __construct($method, UriInterface $uri, $headers, $cookies, $parameters, $body, $files)
		{
			$this->method = $method;
      $this->uri = $uri;
      //$this->headers = $headers;
      $this->cookies = $cookies;
      $this->parameters = $parameters;
      $this->body = $body;
      $this->files = $files;

			$this->attributes = array();
		  $this->dispatched = false;

			foreach($headers as $key => $value)
			{
        $this->headers[strtolower($key)] = $value;
			}
		}

		/**
     * Create new HTTP request with data extracted from the application
     * Environment object
     *
     * @param  Environment $environment The WebAppX Environment
     * @return static
     */
		public static function createFromEnvironment(Environment $environment)
		{
		  // Get request method
			$method = $environment->get('REQUEST_METHOD');

			// Construct uri from environment
			$uri = Uri::createFromEnvironment($environment);

			$headers = getallheaders();
			$cookies = $_COOKIE;
			$parameters = array();

			// Open a stream to temp
      $stream = fopen('php://temp', 'w+');
      // Copy input to temp
      stream_copy_to_stream(fopen('php://input', 'r'), $stream);
      // Rewind
      rewind($stream);

			$body = new Stream($stream);

			$files = $_FILES;

			// Create new instance
			$request = new static($method, $uri, $headers, $cookies, $parameters, $body, $files);

			return $request;
		}

    /**
		 * Get HTTP request method
		 *
		 * @param void
		 * @return string
		 */
		public function getMethod()
		{
		  return $this->method;
		}

		public function getUri()
		{
		  return $this->uri;
		}




    public function getParsedBody()
    {
      $body = (string)$this->body;

      parse_str($body, $params);

      return $params;
    }

    public function getQueryParams()
    {
      $queryParams = [];

      if(!is_null($this->uri))
        parse_str($this->uri->getQuery(), $queryParams);

      return $queryParams;
    }

    public function getServerParams()
    {
      return $_SERVER;
    }


	  /*******************************************************************************
     * Attributes
     ******************************************************************************/

    public function getAttributes()
    {
      return $this->attributes;
    }

    public function getAttribute($name)
    {
      if(isset($this->attributes[$name]))
        return $this->attributes[$name];
      else
        return [];
    }

    public function setAttribute($name, $value)
    {
      $clone = clone($this);
      $clone->attributes[$name] = $value;

      return $clone;
    }

    public function addAttribute($name, $value)
    {
      $clone = clone($this);

      if(isset($clone->attributes[$name]))
      {
        if(!is_array($clone->attributes[$name]))
          $clone->attributes[$name] = array($clone->attributes[$name]);

        $clone->attributes[$name][] = $value;
      }
      else
      {
        $clone->attributes[$name] = $value;
      }

      return $clone;
    }

    public function hasAttribute($name)
    {
      return isset($this->attributes[$name]);
    }

		/**
     * Get parameters
     *
     * @param void
     * @return array
     */
    public function getParameters()
    {
      return $this->parameters;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     * @return void
     */
    public function setParameters($parameters)
    {
      $this->parameters = $parameters;
    }

		/**
		 * Retrieve a value from the user parameters or one of the super globals
		 *
		 * @param string $key
		 * @return string
		 */
		public function get($key)
		{
			switch(true)
			{
				case isset($this->parameters[$key]):
					return $this->parameters[$key];
				case isset($_GET[$key]):
					return $_GET[$key];
				case isset($_POST[$key]):
					return $_POST[$key];
				case isset($_COOKIE[$key]):
					return $_COOKIE[$key];
				case isset($_SERVER[$key]):
					return $_SERVER[$key];
				case isset($_ENV[$key]):
					return $_ENV[$key];
				default:
					return null;
			}
		}

		/**
		 * Check if a key exists in the user parameters or one of the super globals
		 *
		 * @param string $key
		 * @return boolean
		 */
		public function has($key)
		{
			switch(true)
			{
				case isset($this->parameters[$key]):
				case isset($_GET[$key]):
				case isset($_POST[$key]):
				case isset($_COOKIE[$key]):
				case isset($_SERVER[$key]):
				case isset($_ENV[$key]):
					return true;
				default:
					return false;
			}
		}

		public function hasCookie($key)
		{
		  return isset($_COOKIE[$key]);
		}

		/**
		 * Retrieve a value from the user parameters
		 *
		 * @param string $key
		 * @param string $default
		 * @return string
		 */
		public function getParameter($key, $default = null)
		{
			if(isset($this->parameters[$key]))
				return $this->parameters[$key];

			return $default;
		}

		/**
		 * Set a value in the user parameters
		 *
		 * @param string $key
		 * @param string $value
		 * @return void
		 */
		public function setParameter($key, $value)
		{
			if(isset($this->parameters[$key]) && is_null($value))
      	unset($this->parameters[$key]);
			elseif(!is_null($value))
				$this->parameters[$key] = $value;
		}

		/**
		 * Retrieve a value from the $_GET super global
		 *
		 * @param string $key
		 * @param string $default
		 * @return string
		 */
		public function getQuery($key, $default = null)
		{
			return isset($_GET[$key]) ? $_GET[$key] : $default;
		}

		/**
		 * Set a value in the $_GET super global
		 *
		 * @param string $key
		 * @param string $value
		 * @return void
		 */
		public function setQuery($key, $value)
		{
			$_GET[$key] = $value;
		}

		/**
		 * Retrieve a value from the $_POST super global
		 *
		 * @param string $key
		 * @param string $default
		 * @return string
		 */
		public function getPost($key, $default = null)
		{
			return isset($_POST[$key]) ? $_POST[$key] : $default;
		}

		/**
		 * Retrieve a value from the $_COOKIE super global
		 *
		 * @param string $key
		 * @param string $default
		 * @return string
		 */
		public function getCookie($key, $default = null)
		{
			return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
		}

		/**
		 * Retrieve a value from the $_SERVER super global
		 *
		 * @param string $key
		 * @param string $default
		 * @return string
		 */
		public function getServer($key, $default = null)
		{
			return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
		}

		/**
		 * Retrieve a value from the $_ENV super global
		 *
		 * @param string $key
		 * @param string $default
		 * @return string
		 */
		public function getEnv($key, $default = null)
		{
			return isset($_ENV[$key]) ? $_ENV[$key] : $default;
		}




		/**
		 * Checks if request method is a GET request
		 *
		 * @param void
		 * @return boolean
		 */
		public function isGet()
		{
			return ($this->getMethod() == 'GET');
		}

		/**
		 * Checks if request method is a POST request
		 *
		 * @param void
		 * @return boolean
		 */
		public function isPost()
		{
			return ($this->getMethod() == 'POST');
		}

		/**
		 * Checks if request method is a PUT request
		 *
		 * @param void
		 * @return boolean
		 */
		public function isPut()
		{
			return ($this->getMethod() == 'PUT');
		}

	 /**
		 * Checks if request method is a PATCH request
		 *
		 * @param void
		 * @return boolean
		 */
		public function isPatch()
		{
			return ($this->getMethod() == 'PATCH');
		}

		/**
		 * Checks if request method is a DELETE request
		 *
		 * @param void
		 * @return boolean
		 */
		public function isDelete()
		{
			return ($this->getMethod() == 'DELETE');
		}

		/**
		 * Checks if request method is a HEAD request
		 *
		 * @param void
		 * @return boolean
		 */
		public function isHead()
		{
			return ($this->getMethod() == 'HEAD');
		}

		/**
		 * Checks if request method is a OPTIONS request
		 *
		 * @param void
		 * @return boolean
		 */
		public function isOptions()
		{
			return ($this->getMethod() == 'OPTIONS');
		}

		/**
		 * Checks if request method is a XMLHttpRequest
		 *
		 * @param void
		 * @return boolean
		 */
		public function isXmlHttpRequest()
		{
			return ($this->getServer('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest');
		}

    public function isDispatched()
    {
      return $this->dispatched;
    }

		public function setDispatched($dispatched)
		{
		  $this->dispatched = $dispatched;
		}

	}

?>