<?php

namespace ITE\MailBundle\Token;

/**
 * Class Context
 * @package ITE\MailBundle\Token
 */
class Context {
    /**
     * @var string The context name
     */
    protected $name;

    /**
     * @var mixed The context data
     */
    protected $data;

    /**
     * @var bool
     */
    protected $test;

    /**
     * @param $data
     * @param $name
     * @param bool $test
     */
    function __construct($data, $name, $test = false)
    {
        $this->data = $data;
        $this->name = $name;
        $this->test = $test;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData($data) {
        $this->data = $data;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isTest()
    {
        return $this->test;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

} 