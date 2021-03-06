<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Request\Web;

/**
 * Default request
 */
class DefaultRequest implements RequestInterface
{
    /**
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var string
     */
    protected $httpVersion = '1.0';

    /**
     * @var Uri
     */
    protected $uri;

    /**
     * @var HeadersBag
     */
    protected $headers;

    /**
     * @var CookiesBag
     */
    protected $cookies;

    /**
     * @var Proxy
     */
    protected $proxy;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->headers = new HeadersBag;
        $this->cookies = new CookiesBag;
    }

    /**
     * {@inheritDoc}
     */
    public function setHeaders(HeadersBag $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * {@inheritDoc}
     */
    public function setCookies(CookiesBag $cookies)
    {
        $this->cookies = $cookies;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * {@inheritDoc}
     */
    public function setUri($uri)
    {
        if ($uri instanceof Uri) {
            $this->uri = $uri;
        } else {
            $this->uri = Uri::parseFromString($uri);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * {@inheritDoc}
     */
    public function setProxy(Proxy $proxy)
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * {@inheritDoc}
     */
    public function setMethod($method)
    {
        $method = strtoupper($method);

        $allowedMethods = array('OPTIONS', 'GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'TRACE', 'LINK', 'UNLINK', 'CONNECT');
        if (!in_array($method, $allowedMethods)) {
            throw new \InvalidArgumentException(sprintf(
                'Undefined method "%s". Allowed methods: "%s"',
                $method,
                implode('", "', $allowedMethods)
            ));
        }

        $this->method = $method;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritDoc}
     */
    public function setHttpVersion($httpVersion)
    {
        $this->httpVersion = $httpVersion;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getHttpVersion()
    {
        return $this->httpVersion;
    }

    /**
     * {@inheritDoc}
     */
    public function prepare()
    {
        // Added Accept header if not exists
        if (!$this->headers->has('Accept')) {
            $this->headers['Accept'] = '*/*';
        }

        // Add cookies
        if (count($this->cookies)) {
            $this->headers['Cookie'] = $this->cookies;
        }
    }

    /**
     * __toString
     */
    public function __toString()
    {
        if (!$this->uri) {
            throw new \RuntimeException('Undefined target request URI.');
        }

        $this->prepare();

        return $this->method . ' ' . ($this->uri->getPath() . ($this->uri->getQuery() ? '?' . implode('&', $this->uri->getQuery()) : '')) . ' HTTP/' . $this->httpVersion .  "\r\n" .
            'Host: ' . $this->uri->getHost() . "\r\n".
            ((string) $this->headers) .
            "\r\n\r\n";
    }
}