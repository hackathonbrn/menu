<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name = "users")
 */
class User extends AbstractEntity implements AdvancedUserInterface, \Serializable
{
    const PUBLIC_PATH = 'web';
    const UPLOAD_DIR = '/images/users';

    /**
     * @var int
     * @ORM\Column(name = "id", type = "integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name = "password", type = "string")
     */
    protected $password;

    /**
     * @var ArrayCollection|Role[]
     * @ORM\ManyToMany(targetEntity = "AppBundle\Entity\Role", inversedBy = "users")
     * @ORM\JoinTable(name = "user_roles",
     *   joinColumns = {@ORM\JoinColumn(name = "userid", referencedColumnName = "id")},
     *   inverseJoinColumns = {@ORM\JoinColumn(name = "roleid", referencedColumnName = "id")}
     * )
     */
    protected $roles;

    /**
     * @var string
     * @ORM\Column(name = "salt", type = "string")
     */
    protected $salt;

    /**
     * @var string
     * @ORM\Column(name = "username", type = "string", unique = true)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(name = "userfio", type = "string")
     */
    protected $userfio;

    /**
     * @var string
     * @ORM\Column(name = "email", type = "string")
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name = "phone", type = "string", nullable=true)
     */
    protected $phone;

    /**
     * @var boolean
     * @ORM\Column(name = "deleted", type = "boolean")
     */
    protected $deleted;
   
    /**
     * @var ArrayCollection|UserMenu[]
     * @ORM\OneToMany(targetEntity = "AppBundle\Entity\UserMenu", mappedBy = "user", cascade = {"persist", "remove"})
     */
    protected $usermenus;

    /**
     * @var ArrayCollection|Dish[]
     * @ORM\OneToMany(targetEntity = "AppBundle\Entity\Dish", mappedBy = "author", cascade = {"persist", "remove"})
     */
    protected $dishes;
    

    /**
     * @var string
     * @ORM\Column(name = "image", type = "string",  nullable = true)
     */
    protected $image;

    /** @var UploadedFile */
    protected $file;

    /** @var string */
    protected $path;


    public function __construct()
    {
        parent::__construct();
        $this->roles = new ArrayCollection();
        $this->usermenus= new ArrayCollection();
        $this->dishes = new ArrayCollection();
        $this->dishmenus = new ArrayCollection();
        $this->salt = self::generateSalt();
        $this->deleted = false;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        if ($this->isDeleted()) {
            return false;
        }
        else {
            return true;
        }

    }

    public function eraseCredentials()
    {
        return $this;
    }


    /**
     * @return string
     */
    public static function generateSalt()
    {
        $symbols = '0123456789abcdef';
        $salt = '';

        foreach (range(1, 32) as $i) $salt .= $symbols[mt_rand(0, 15)];

        return $salt;
//        return openssl_random_pseudo_bytes(32);
    }


    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return array|Role[]
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * @return ArrayCollection|Role[]
     */
    public function getRolesCollection()
    {
        return $this->roles;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function serialize()
    {
        return serialize(array(
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'salt' => $this->salt,
            'deleted' =>$this->deleted
        ));
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function setRoles($roles)
    {
        $this->roles->clear();

        foreach ($roles as $role) $this->roles->add($role);

        return $this;
    }

    /**
     * @param string $salt
     * @return $this
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function unserialize($serialized)
    {
        $unserialized = unserialize($serialized);
        $this->id = $unserialized['id'];
        $this->username = $unserialized['username'];
        $this->password = $unserialized['password'];
        $this->salt = $unserialized['salt'];
        $this->deleted = $unserialized['deleted'];

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getUserfio()
    {
        return $this->userfio;
    }

    /**
     * @param string $userfio
     */
    public function setUserfio($userfio)
    {
        $this->userfio = $userfio;
    }
    

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return File|null
     */
    public function upload()
    {
        if (is_null($this->file)) return null;

        $this->image = sha1(uniqid(mt_rand(), true)).'.'.$this->file->getClientOriginalExtension();
        $basename = basename($this->image, $this->file->getClientOriginalExtension());
        $path = $this->image;
        $i = 1;

        while (file_exists($this->getUploadRootDir() . '/' . $path)) {
            $path = $basename . $i . '.' . $this->file->getClientOriginalExtension();
            $i++;
        }

        $this->path = self::UPLOAD_DIR . '/' . $path;

        return $this->file->move($this->getUploadRootDir(), $path);
    }

    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../' . self::PUBLIC_PATH . self::UPLOAD_DIR;
    }
    

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return UserMenu[]|ArrayCollection
     */
    public function getUsermenus()
    {
        return $this->usermenus;
    }

    /**
     * @param UserMenu[]|ArrayCollection $usermenus
     */
    public function setUsermenus($usermenus)
    {
        $this->usermenus = $usermenus;
    }


}