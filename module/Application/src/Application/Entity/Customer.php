<?php
namespace Application\Entity;

use Application\Library\Exception\ValidationException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="`Customers`")
 */
class Customer
{
    const STATUS_ACTIVE = 'active';
    const STATUS_BLOCKED = 'blocked';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    public $id;

    /** @ORM\Column(type="string") */
    protected $first_name;


    /** @ORM\Column(type="string") */
    protected $last_name;


    /** @ORM\Column(type="string") */
    protected $phone;

    /** @ORM\Column(type="string") */
    protected $address;

    /** @ORM\Column(type="string") */
    protected $status;

    /**
     * @ORM\OneToMany(targetEntity="Call", mappedBy="customer")
     */
    protected $calls;


    /*
     * Validate status
     */

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        if (!in_array($status, array(self::STATUS_ACTIVE, self::STATUS_BLOCKED))) {
            throw new ValidationException(["status" => "Invalid status"], 400);
        }

        $this->status = $status;
    }

    /**
     * Return the associated calls
     */
    public function fetchCalls()
    {
        return $this->calls;
    }
}