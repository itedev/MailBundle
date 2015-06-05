<?php

namespace ITE\MailBundle\Data;


use Jroc\CoreBundle\Entity\Mail;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class MailEvent
 * @package ITE\MailBundle\Data
 */
class MailEvent extends Event {

    /**
     * @var Mail
     */
    protected $mail;

    /**
     * @param Mail $mail
     */
    public function setMail(Mail $mail)
    {
        $this->mail = $mail;
    }

    /**
     * @return Mail
     */
    public function getMail()
    {
        return $this->mail;
    }



}