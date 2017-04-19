<?php

namespace Swing;

class Response
{
    protected $content;

    function __construct($content = null)
    {
        $this->setContent($content);
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set headers
     * @param array $headers
     */
    public function headers(array $headers): void
    {
        foreach ($headers as $key => $value) {
            header($key . ': ' . $value);
        }
    }

    public function json()
    {
        $this->headers(['Content-Type' => 'application/json']);

        echo json_encode($this->content);

        return $this;
    }
}