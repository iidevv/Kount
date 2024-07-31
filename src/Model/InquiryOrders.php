<?php

namespace Iidev\Kount\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="kount_inquiry_orders")
 */
class InquiryOrders extends \XLite\Model\AEntity
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $orderid;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $transactionId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $warnings;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $score;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $omniscore;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $ipAddress;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $userAgent;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $auto;
   

    /**
     * Get the value of orderid
     */
    public function getOrderid(): int
    {
        return $this->orderid;
    }

    /**
     * Set the value of orderid
     */
    public function setOrderid($orderid): self
    {
        $this->orderid = $orderid;

        return $this;
    }

    /**
     * Get the value of transactionId
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * Set the value of transactionId
     */
    public function setTransactionId(string $transactionId): self
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get the value of warnings
     */
    public function getWarnings(): string
    {
        return $this->warnings;
    }

    /**
     * Set the value of warnings
     */
    public function setWarnings(string $warnings): self
    {
        $this->warnings = $warnings;

        return $this;
    }

    /**
     * Get the value of score
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * Set the value of score
     */
    public function setScore($score): self
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get the value of auto
     */
    public function getAuto(): string
    {
        return $this->auto;
    }

    /**
     * Set the value of auto
     */
    public function setAuto(string $auto): self
    {
        $this->auto = $auto;

        return $this;
    }

    /**
     * Get the value of userAgent
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * Set the value of userAgent
     */
    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Get the value of ipAddress
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * Set the value of ipAddress
     */
    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get the value of omniscore
     */
    public function getOmniscore(): int
    {
        return $this->omniscore;
    }

    /**
     * Set the value of omniscore
     */
    public function setOmniscore($omniscore): self
    {
        $this->omniscore = $omniscore;

        return $this;
    }
}