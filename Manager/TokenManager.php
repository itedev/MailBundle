<?php

namespace ITE\MailBundle\Manager;

use ITE\MailBundle\Event\MailEvent;
use ITE\MailBundle\Extension\TokenExtensionInterface;
use ITE\MailBundle\Token\Context;
use ITE\MailBundle\Token\Token;
use Symfony\Bundle\AsseticBundle\Factory\AssetFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * Class TokenManager
 * @package ITE\MailBundle\Manager
 */
class TokenManager
{

    /**
     * @var TokenExtensionInterface[] Extensions
     */
    protected $extensions = [];

    /**
     * @var \ITE\MailBundle\Token\Token[] Global tokens
     */
    protected $globals = [];

    /**
     * @var \Symfony\Bundle\AsseticBundle\Factory\AssetFactory
     */
    private $af;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->af = $container->get('assetic.asset_factory');
    }


    /**
     * Add extension to token manager
     *
     * @param TokenExtensionInterface $extension
     */
    public function addExtension(TokenExtensionInterface $extension)
    {
        $this->extensions[] = $extension;
        $this->globals = array_merge($this->globals, $extension->getGlobals());
    }

    /**
     * Tokenize string and replace all tokens
     *
     * @param string $data The data to tokenize
     * @param Context $context The execution context
     *
     * @return string
     */
    public function tokenize($data, Context $context = null)
    {
        $tokens = $this->getContextTokens($context);
        foreach ($tokens as $token) {
            $name = $token->getToken();
            $value = $token->getValue();
            $data = str_replace('[' . $name . ']', $value, $data);
        }

        return $data;
    }

    /**
     * Returns full tokens, available for context
     *
     * @param Context $context
     *
     * @return array|\ITE\MailBundle\Token\Token[]
     */
    public function getContextTokens(Context $context = null)
    {
        if (!$context) {
            return $this->globals;
        }
        $tokens = $this->globals;
        foreach ($this->extensions as $extension) {
            if ($context->isTest()) {
                $extension->setContext($context);
            }
            $tokens = array_merge($tokens, $extension->getTokens($context));
        }

        $result = [];
        foreach ($tokens as $token) {
            $result[$token->getToken()] = $token;
        }

        return $result;
    }

    /**
     * @param MailEvent $e
     */
    public function onMail(MailEvent $e) //this is 'exp.mail'
    {
        $mail = $e->getMail();
        $contexts = [];
        if ($params = $mail->getParams()) {
            if (isset($params['token_context'])) {
                $contexts = $params['token_context'];
                if (!is_array($contexts)) {
                    $contexts = [$contexts];
                }
            }
        }

        $body = $mail->getBody();
        foreach ($contexts as $context) {
            $body = $this->tokenize($body, $context);
        }
        $css = $this->af->createAsset($this->container->getParameter('ite_mail.styles'))->dump();
        $processor = new CssToInlineStyles($body, $css);
        // process & restore encoded variables in href's
        $message = preg_replace('/%5B(.*)%5D/', '[$1]', $processor->convert());
        $mail->setBody($message);

        $subject = $mail->getSubject();
        foreach ($contexts as $context) {
            $subject = $this->tokenize($subject, $context);
        }
        $mail->setSubject($subject);

        $e->setMail($mail);
    }
}
