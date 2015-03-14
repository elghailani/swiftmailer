<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Creates MIME headers.
 *
 * @author     Chris Corbyn
 */
class Swift_Mime_SimpleHeaderFactory implements Swift_Mime_HeaderFactory
{
    /** The HeaderEncoder used by these headers */
    private $encoder;

    /** The Encoder used by parameters */
    private $paramEncoder;

    /** The Grammar */
    private $grammar;

    /** The charset of created Headers */
    private $charset;

    /**
     * Creates a new SimpleHeaderFactory using $encoder and $paramEncoder.
     *
     * @param Swift_Mime_HeaderEncoder $encoder
     * @param Swift_Encoder            $paramEncoder
     * @param Swift_Mime_Grammar       $grammar
     * @param string|null              $charset
     */
    public function __construct(Swift_Mime_HeaderEncoder $encoder, Swift_Encoder $paramEncoder, Swift_Mime_Grammar $grammar, $charset = null)
    {
        $this->encoder = $encoder;
        $this->paramEncoder = $paramEncoder;
        $this->grammar = $grammar;
        $this->charset = $charset;
    }

    /**
     * Create a new Mailbox Header with a list of $addresses.
     *
     * @param string            $name
     * @param array|string|null $addresses
     *
     * @return Swift_Mime_Header
     */
    public function createMailboxHeader($name, $addresses = null)
    {
        $header = new Swift_Mime_Headers_MailboxHeader($name, $this->encoder, $this->grammar);
        if (isset($addresses)) {
            $header->setFieldBodyModel($addresses);
        }
        $this->setHeaderCharset($header);

        return $header;
    }

    /**
     * Create a new Date header using $timestamp (UNIX time).
     * @param string       $name
     * @param int|null     $timestamp
     *
     * @return Swift_Mime_Header
     */
    public function createDateHeader($name, $timestamp = null)
    {
        $header = new Swift_Mime_Headers_DateHeader($name, $this->grammar);
        if (isset($timestamp)) {
            $header->setFieldBodyModel($timestamp);
        }
        $this->setHeaderCharset($header);

        return $header;
    }

    /**
     * Create a new basic text header with $name and $value.
     *
     * @param string $name
     * @param string $value
     *
     * @return Swift_Mime_Header
     */
    public function createTextHeader($name, $value = null)
    {
        $header = new Swift_Mime_Headers_UnstructuredHeader($name, $this->encoder, $this->grammar);
        if (isset($value)) {
            $header->setFieldBodyModel($value);
        }
        $this->setHeaderCharset($header);

        return $header;
    }

    /**
     * Create a new ParameterizedHeader with $name, $value and $params.
     *
     * @param string $name
     * @param string $value
     * @param array  $params
     *
     * @return Swift_Mime_ParameterizedHeader
     */
    public function createParameterizedHeader($name, $value = null,
        $params = array())
    {
        $header = new Swift_Mime_Headers_ParameterizedHeader($name,
            $this->encoder, (strtolower($name) == 'content-disposition')
                ? $this->paramEncoder
                : null,
                $this->grammar
            );
        if (isset($value)) {
            $header->setFieldBodyModel($value);
        }
        foreach ($params as $k => $v) {
            $header->setParameter($k, $v);
        }
        $this->setHeaderCharset($header);

        return $header;
    }

    /**
     * Create a new ID header for Message-ID or Content-ID.
     *
     * @param string       $name
     * @param string|array $ids
     *
     * @return Swift_Mime_Header
     */
    public function createIdHeader($name, $ids = null)
    {
        $header = new Swift_Mime_Headers_IdentificationHeader($name, $this->grammar);
        if (isset($ids)) {
            $header->setFieldBodyModel($ids);
        }
        $this->setHeaderCharset($header);

        return $header;
    }

    /**
     * Create a new Path header with an address (path) in it.
     *
     * @param string $name
     * @param string $path
     *
     * @return Swift_Mime_Header
     */
    public function createPathHeader($name, $path = null)
    {
        $header = new Swift_Mime_Headers_PathHeader($name, $this->grammar);
        if (isset($path)) {
            $header->setFieldBodyModel($path);
        }
        $this->setHeaderCharset($header);

        return $header;
    }

    /**
     * Notify this observer that the entity's charset has changed.
     *
     * @param string $charset
     */
    public function charsetChanged($charset)
    {
        $this->charset = $charset;
        $this->encoder->charsetChanged($charset);
        $this->paramEncoder->charsetChanged($charset);
    }

    /**
    * Make a deep copy of object
    */
    public function __clone()
    {
        $this->encoder = clone $this->encoder;
        $this->paramEncoder = clone $this->paramEncoder;
    }

    /** Apply the charset to the Header */
    private function setHeaderCharset(Swift_Mime_Header $header)
    {
        if (isset($this->charset)) {
            $header->setCharset($this->charset);
        }
    }
}
