<?php

namespace ITE\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MailLog
 *
 * @ORM\Table(name="mail_log")
 * @ORM\Entity
 */
class Mail
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(length=255, name="from_email", type="string")
     */
    private $fromEmail;

    /**
     * @var string
     *
     * @ORM\Column(length=255, name="to_email", type="string")
     */
    private $toEmail;

    /**
     * @var array
     *
     * @ORM\Column(name="bcc", type="array", nullable=true)
     */
    private $bcc;

    /**
     * @var string
     *
     * @ORM\Column(length=255, name="subject", nullable=true, type="string")
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_at", type="datetime")
     */
    private $sentAt;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string $userType
     */
    private $userType;

    /**
     * @var array
     */
    private $params;

    /**
     * @var string
     */
    private $contentType;

    /**
     * Public constructor
     */
    public function __construct(){
        $this->sentAt = new \DateTime();
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get userType
     *
     * @return string
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * Set userType
     *
     * @param string $userType
     * @return Mail
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fromEmail
     *
     * @param string $fromEmail
     * @return Mail
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
    
        return $this;
    }

    /**
     * Get fromEmail
     *
     * @return string 
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * Set toEmail
     *
     * @param string $toEmail
     * @return Mail
     */
    public function setToEmail($toEmail)
    {
        $this->toEmail = $toEmail;
    
        return $this;
    }

    /**
     * Get toEmail
     *
     * @return string 
     */
    public function getToEmail()
    {
        return $this->toEmail;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Mail
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    
        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Mail
     */
    public function setBody($body)
    {
        $this->body = $body;
    
        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set sentAt
     *
     * @param \DateTime $sentAt
     * @return Mail
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;
    
        return $this;
    }

    /**
     * Get sentAt
     *
     * @return \DateTime
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * Get bcc
     *
     * @return array
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * Set bcc
     *
     * @param mixed $bcc
     * @return Mail
     */
    public function setBcc($bcc)
    {
        if (is_array($bcc) || is_null($bcc)) {
            $this->bcc = $bcc;
        } elseif (is_string($bcc)) {
            $this->bcc = [$bcc => null];
        }

        return $this;
    }

    /**
     * @param string $address
     * @param null $name
     * @return $this
     */
    public function addBcc($address, $name = null)
    {
        if (!is_array($this->bcc)) {
            $this->bcc = [];
        }
        $this->bcc[$address] = $name;

        return $this;
    }

    /**
     * Returns mail as array
     *
     * @return array
     */
    public function toArray(){
        return array_merge(
            [
                'to'      => $this->toEmail,
                'from'    => $this->fromEmail,
                'subject' => $this->subject,
                'body'    => $this->body,
            ],
            $this->params
        );
    }

}
