<?php

namespace Jsanbae\SIIAPI;

use Jsanbae\SIIAPI\APICredentialAttributes;
use Jsanbae\SIIAPI\Contracts\Arrayable;

class APICredential implements Arrayable
{
    private $user;
    private $password;
    private $attributes;

    public function __construct(string $_user, string $_password, APICredentialAttributes $_attributes = null)
    {
        $this->user = $_user;
        $this->password = $_password;
        $this->attributes = $_attributes;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function attributes(): ?APICredentialAttributes
    {
        return $this->attributes;
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'password' => $this->password,
            'attributes' => $this->attributes->toArray()
        ];
    }
}
