<?php

namespace ITE\MailBundle\Extension;


use Doctrine\Common\Collections\ArrayCollection;
use ITE\MailBundle\Extension\TokenExtensionInterface;
use ITE\MailBundle\Token\Context;
use ITE\MailBundle\Token\Token;

/**
 * Class BaseExtension
 * @package ITE\MailBundle\Extension
 */
abstract class BaseExtension implements TokenExtensionInterface
{

    /**
     * @param Context $context
     * @return mixed
     */
    public function setContext(Context $context)
    {

    }

    /**
     * Get object from context data.
     *
     * @param $className
     * @param $data
     * @param $key
     * @return null
     */
    protected function getObjectFromData($className, $data, $key)
    {
        if ($data instanceof $className) {
            return $data;
        }

        if (is_array($data) && array_key_exists($key, $data)) {
            $object = $data[$key];

            if ($object instanceof $className) {
                return $object;
            }
        }

        return null;
    }

    /**
     * Get scalar value from context data.
     *
     * @param string $key
     * @param array|object|null $data
     * @param bool $emptyStringIfNull
     * @return null|string
     */
    protected function getScalarValueFromData($key, $data, $emptyStringIfNull = true)
    {
        if (!is_array($data) || !array_key_exists($key, $data)) {
            return $emptyStringIfNull ? '' : null;
        }

        return $data[$key];
    }
}
