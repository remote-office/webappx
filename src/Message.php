<?php

  namespace WebAppX;

  use LibX\Stream\StreamInterface;

  abstract class Message implements MessageInterface
  {
    /**
     * Protocol version
     *
     * @var string
     */
    protected $protocolVersion = '1.1';

    protected $headers = [];
    protected $body;

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion()
    {
      return $this->protocolVersion;
    }

    /**
     * Disable magic setter to ensure immutability
     */
    public function __set($name, $value)
    {
      // Do nothing
    }

    /*******************************************************************************
     * Headers
     ******************************************************************************/

    public function getHeaders()
    {
      return $this->headers;
    }

    public function getHeader($name)
    {
      if(isset($this->headers[$name]))
        return $this->headers[$name];
      else
        return [];
    }

    public function setHeader($name, $value)
    {
      $clone = clone($this);
      $clone->headers[$name] = $value;

      return $clone;
    }

    public function addHeader($name, $value)
    {
      $clone = clone($this);

      if(isset($clone->headers[$name]))
      {
        if(!is_array($clone->headers[$name]))
          $clone->headers[$name] = array($clone->headers[$name]);

        $clone->headers[$name][] = $value;
      }
      else
      {
        $clone->headers[$name] = $value;
      }

      return $clone;
    }

    public function hasHeader($name)
    {
      return isset($this->headers[$name]);
    }

    public function getBody()
    {
      return $this->body;
    }

    public function setBody(StreamInterface $body)
    {
      $clone = clone $this;
      $clone->body = $body;

      return $clone;
    }

    public function hasBody()
    {
      return !is_null($body);
    }
  }

?>