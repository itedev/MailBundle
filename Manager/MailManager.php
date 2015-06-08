<?php

namespace ITE\MailBundle\Manager;

use Doctrine\ORM\EntityManager;
use ITE\MailBundle\Entity\Mail;
use ITE\MailBundle\Event\MailEvent;
use ITE\MailBundle\Token\Context;
use ITE\MailBundle\Token\Token;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Html2Text\Html2Text;

/**
 * Class MailManager
 * @package ITE\MailBundle\Manager
 */
class MailManager
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var array
     */
    protected $parameters;
    /**
     * @var \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher
     */
    protected $ed;
    /**
     * @var \Twig_Environment
     */
    protected $twig;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Public constructor.
     *
     * @param ContainerInterface $container
     * @internal param \Swift_Mailer $mailer
     * @internal param EventDispatcherInterface $ed
     * @internal param \Twig_Environment $twig
     * @internal param EntityManager $em
     * @internal param array $parameters
     * @internal param $amazonBaseUrl
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->mailer = $container->get('mailer');
        $this->ed = $container->get('event_dispatcher');
        $this->twig = $container->get('twig');
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * Build mail by mail settings
     *
     * @param       $type
     * @param null $to
     * @param null $subject
     * @param array $parameters
     * @param null $from
     * @param string $emailTemplate
     * @param string $layout
     * @param null $bcc
     * @return Mail
     */
    public function getMail($type, $to = null, $subject = null, $parameters = array(), $from = null, $emailTemplate = null, $bcc = null, $layout = '')
    {
        $mail = new Mail();
        $mail->setType($type);

        $mail->setToEmail($to);

        if (!$from) {
            $from = $this->parameters['mail_noreply'];
        }
        $mail->setFromEmail($from);

        $mail->setParams($parameters);
        $mail->setSubject($subject);
        $this->buildMail($mail, $emailTemplate, $layout);
        $mail->setContentType('text/html');
        $event = new MailEvent();
        $event->setMail($mail);
        $this->ed->dispatch('ite.mail', $event);
        $mail = $event->getMail();

        $body = $mail->getBody();
        $mail->setBody($body);
        $mail->setBcc($bcc);

        return $mail;
    }

    /**
     * Sends email message
     *
     * @param       $type
     * @param       $to
     * @param null $subject
     * @param array $parameters
     * @param string $from
     * @param string $emailTemplate
     * @param string $layout
     * @param null $bcc
     */
    public function mail($type, $to = null, $subject = null, $parameters = array(), $from = null, $emailTemplate = null, $bcc = null, $layout = '')
    {
        if (!$from) {
            $from = $this->container->getParameter('ite_mail.from_email');
        }
        if (!$bcc) {
            $bcc = $this->container->getParameter('ite_mail.bcc_email');
        }

        $mail = $this->getMail($type, $to, $subject, $parameters, $from, $emailTemplate, $bcc, $layout);
        $this->sendMail($mail);
    }

    /**
     * @param $to
     * @param $subject
     * @param $body
     * @param string $contentType
     * @param null $bcc
     * @param null $from
     */
    public function simpleMail($to, $subject, $body, $contentType = 'text/html', $bcc = null, $from = null)
    {
        $mail = new Mail();
        $mail->setToEmail($to);
        $mail->setSubject($subject);
        $mail->setBody($body);
        $mail->setContentType($contentType);
        $mail->setBcc($bcc);
        if (!$from) {
            $from = $this->container->getParameter('mail_from');
        }
        $mail->setFromEmail($from);

        $this->sendMail($mail);
    }

    /**
     * Builds mail to send.
     *
     * @param Mail $mail
     * @param string $emailTemplate
     * @param string $layout
     *
     * @throws \InvalidArgumentException
     */
    protected function buildMail(Mail $mail, $emailTemplate = null, $layout = '')
    {
        $type = $mail->getType();
        if (!$layout) {
            $layout = $this->container->getParameter('ite_mail.template_folder') . ":{$type}.html.twig";
        }
        if ($type && $layout) {
            $template = $this->twig->resolveTemplate($layout);

            if ($template) {
                $content = $template->render([]);

                $mail->setBody($content);
                $mail->setContentType('text/html; charset=UTF-8');
                if (!$mail->getSubject()) {
                    $mail->setSubject('Hello');
                }
            } else {
                throw new \InvalidArgumentException('Template "' . $type . '" cannot be found.');
            }
        }
    }

    /**
     * Converts Mail object to \Swift_Message
     *
     * @param Mail $mail
     *
     * @return \Swift_Message
     */
    protected function toSwift(Mail $mail)
    {
        /** @var \Swift_Message $swift */
        $swift = $this->mailer->createMessage();
        $swift->setTo($mail->getToEmail());
        $swift->setFrom($mail->getFromEmail());
        $swift->setSubject($mail->getSubject());
        $swift->setBody($mail->getBody());
        $swift->setContentType($mail->getContentType());
        if (null !== $bcc = $mail->getBcc()) {
            $swift->setBcc($bcc);
        }

        // if the default is HTML then add a text version of the email
        if ('text/html' == $mail->getContentType()) {
            $converter = new Html2Text($mail->getBody());
            $swift->addPart($converter->get_text(), 'text/plain');
        }

        return $swift;
    }

    /**
     * Sends mail
     *
     * @param Mail $mail
     */
    protected function sendMail(Mail $mail)
    {
        $swift = $this->toSwift($mail);

        $this->mailer->send($swift);

        if (is_array($mail->getFromEmail())) {
            $mail->setFromEmail(array_keys($mail->getFromEmail())[0]);
        }

        $this->em->persist($mail);
        $this->em->flush($mail);
    }

}
