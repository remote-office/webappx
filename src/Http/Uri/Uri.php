<?php

  namespace WebAppX\Http\Uri;

  use WebAppX\Environment;

  class Uri extends \LibX\Http\Uri\Uri implements UriInterface
  {
    /**
     * Create a Uri from WebAppX environment
     *
     * @param Environment $environment
     * @return Uri
     */
    public static function createFromEnvironment(Environment $environment)
    {
      // Scheme
      $secure = $environment->get('HTTPS');
      $scheme = (empty($secure) || $secure === 'off') ? 'http' : 'https';

      // Authority: Host
      if($environment->has('HTTP_HOST'))
        $host = $environment->get('HTTP_HOST');
      else
        $host = $environment->get('SERVER_NAME');

      // Authority: Port
      $port = (int)$environment->get('SERVER_PORT', 80);

      // Parse url
      $parts = parse_url($environment->get('REQUEST_URI'));

      $path = isset($parts['path']) ? $parts['path'] : '/';
      $query = isset($parts['query']) ? $parts['query'] : '';
      $fragment = isset($parts['fragment']) ? $parts['fragment'] : '';

      // Authority: Username and password
      $user = $environment->get('PHP_AUTH_USER', '');
      $password = $environment->get('PHP_AUTH_PW', '');

      // Build Uri
      $uri = new static($scheme, $host, $port, $path, $query, $fragment, $user, $password);

      return $uri;
    }
  }

?>