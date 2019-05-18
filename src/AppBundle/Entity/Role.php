<?php
/**
 * Created by PhpStorm.
 * User: Mixa
 * Date: 02.04.2017
 * Time: 19:40
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Class Role
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name = "roles")
 */
class Role extends AbstractEntity implements RoleInterface
{
    const ADMIN = 'ROLE_ADMIN';
    const USER = 'ROLE_USER';

    /**
     * @var int
     * @ORM\Column(name = "id", type = "integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    protected $id;
    
    /**
     * @var string
     * @ORM\Column(name = "caption", type = "string")
     */
    protected $caption;

    /**
     * @var string
     * @ORM\Column(name = "role", type = "string", unique = true)
     */
    protected $role;

    /**
     * @var ArrayCollection|User[]
     * @ORM\ManyToMany(targetEntity = "AppBundle\Entity\User", mappedBy = "roles")
     */
    protected $users;

    public function __construct()
    {
        parent::__construct();

        $this->users = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return ArrayCollection|User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param string $caption
     * @return $this
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
