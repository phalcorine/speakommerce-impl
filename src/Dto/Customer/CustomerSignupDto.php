<?php

namespace App\Dto\Customer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerSignupDto
{
    #[NotBlank(message: 'Name is required', normalizer: 'trim')]
    private ?string $name = null;

    #[NotBlank(message: 'Email is required')]
    #[Email(message: 'A valid email address is required')]
    private ?string $email = null;

    #[Length(
        min: 8,
        max: 100,
        normalizer: 'trim',
        minMessage: 'Password can not be less than {{ limit }} characters',
        maxMessage: 'Password can not be more than {{ limit }} characters',
    )]
    private ?string $password = null;

    public static function fromRequest(Request $request): CustomerSignupDto
    {
        return (new self())
            ->setName($request->request->get('name'))
            ->setEmail($request->request->get('email'))
            ->setPassword($request->request->get('password'));
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return CustomerSignupDto
     */
    public function setName(?string $name): CustomerSignupDto
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return CustomerSignupDto
     */
    public function setEmail(?string $email): CustomerSignupDto
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return CustomerSignupDto
     */
    public function setPassword(?string $password): CustomerSignupDto
    {
        $this->password = $password;
        return $this;
    }
}