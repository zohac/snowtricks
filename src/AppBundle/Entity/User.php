<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User.
 *
 * @ORM\Table(name="user")
 *
 * @UniqueEntity(fields="email", message="Email déjà utilisé")
 * @UniqueEntity(fields="username", message="Pseudo déjà utilisé.")
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=60, unique=true)
     *
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=60)
     */
    private $password;

    /**
     * @var string|null
     *
     * @Assert\Length(max=4096)
     * @Assert\Regex(
     *      pattern="/^[a-zA-Z0-9]{6,}$/",
     *      message="Le mot de passe doit comporter au moins 6 caractères, minuscule, majuscule et numérique."
     * )
     */
    private $plainPassword;

    /**
     * @var array|null
     *
     * @ORM\Column(name="roles", type="array", nullable=true)
     */
    private $roles;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateRegistration", type="datetime", nullable=true)
     *
     * @Assert\DateTime()
     */
    private $dateRegistration;

    /**
     * @var string|null
     *
     * @ORM\Column(name="token", type="string", length=64, nullable=true, unique=true)
     */
    private $token;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var Picture
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Picture", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Assert\Valid
     */
    private $avatar;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set plainPassword.
     *
     * @param string|null $plainPassword
     *
     * @return User
     */
    public function setPlainPassword($plainPassword = null)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Get plainPassword.
     *
     * @return string|null
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set roles.
     *
     * @param array|null $roles
     *
     * @return User
     */
    public function setRoles($roles = null)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles.
     *
     * @return array|null
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set dateRegistration.
     *
     * @param \DateTime|null $dateRegistration
     *
     * @return User
     */
    public function setDateRegistration($dateRegistration = null)
    {
        $this->dateRegistration = $dateRegistration;

        return $this;
    }

    /**
     * Get dateRegistration.
     *
     * @return \DateTime|null
     */
    public function getDateRegistration()
    {
        return $this->dateRegistration;
    }

    /**
     * Set token.
     *
     * @param string|null $token
     *
     * @return User
     */
    public function setToken($token = null)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token.
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set salt.
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt.
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials()
    {
    }

    /**
     * Set avatar.
     *
     * @param \AppBundle\Entity\Picture $avatar
     *
     * @return User
     */
    public function setAvatar(\AppBundle\Entity\Picture $avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar.
     *
     * @return \AppBundle\Entity\Picture
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
}
