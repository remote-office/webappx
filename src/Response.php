<?php

	namespace WebAppX;

	use LibX\Stream\Stream;

  /**
 * Response
 *
 * This class represents an HTTP response. It manages
 * the response status, headers, and body
 * according to the PSR-7 standard.
 *
 * @link https://github.com/php-fig/http-message/blob/master/src/MessageInterface.php
 * @link https://github.com/php-fig/http-message/blob/master/src/ResponseInterface.php
 */
	class Response extends Message implements ResponseInterface
	{
	  /**
     * Status code
     *
     * @var int
     */
    protected $status = 200;

	  public function __construct($status = 200)
	  {
	    $this->status = $status;

	    $stream = fopen('php://temp', 'r+');

	    $this->body = new Stream($stream);
	  }

		public function setStatus($status)
		{
		  $clone = clone($this);
		  $clone->status = $status;

		  return $clone;
		}

		public function getStatus()
		{
		  return $this->status;
		}

		public function setRedirect($url, $status = null)
		{
		  $reponseWithRedirect = $this->setHeader('Location', (string)$url);

		  if(is_null($status) && $this->getStatus() === 200)
        $status = 302;

      if(!is_null($status))
        $reponseWithRedirect = $reponseWithRedirect->setStatus($status);

      return $reponseWithRedirect;
		}
	}

?>