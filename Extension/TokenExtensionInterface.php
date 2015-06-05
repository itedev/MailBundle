<?php
namespace ITE\MailBundle\Extension;

use ITE\MailBundle\Token\Context;
use ITE\MailBundle\Token\Token;

/**
 * Interface TokenExtensionInterface
 * @package ITE\MailBundle\Extension
 */
interface TokenExtensionInterface {

    /**
     * Retrieve token list depend on token context
     *
     * @param Context $context
     *
     * @return Token[]
     */
    public function getTokens(Context $context);

    /**
     * Retrieve global token list, not depended on context
     *
     * @return Token[]
     */
    public function getGlobals();

    /**
     * @param Context $context
     * @return mixed
     */
    public function setContext(Context $context);


} 