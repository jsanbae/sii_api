<?php

namespace Jsanbae\SIIAPI;

use Jsanbae\SIIAPI\APICredentialAttributes;
use Jsanbae\SIIAPI\Contracts\Arrayable;

class APICredential implements Arrayable
{
    private $username;
    private $password;
    private $attributes;

    public function __construct(string $_username, string $_password, APICredentialAttributes $_attributes = null)
    {
        $this->username = $_username;
        $this->password = $_password;
        $this->attributes = $_attributes;
    }

    public function getUsername(): string
    {
        return $this->username;
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
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'attributes' => $this->attributes->toArray()
        ];
    }
}
