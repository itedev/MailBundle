<?php

namespace ITE\MailBundle\Token;

/**
 * Class Token
 * @package ITE\MailBundle\Token
 */
class Token {

    /**
     * @var string The token name
     */
    protected $token;

    /**
     * @var string The token value
     */
    protected $value;
    /**
     * @var string The token description
     */
    protected $description;

    /**
     * @param $token
     * @param $value
     * @param $description
     */
    function __construct($token, $value, $description)
    {
        $this->token       = $token;
        $this->value       = $value;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }


}
