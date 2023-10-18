<?php

namespace Jsanbae\SIIAPI;
use Jsanbae\SIIAPI\Contracts\Arrayable;

class APICredentialAttributes implements Arrayable
{
    private $attributes;

    public function __construct($_attributes = [])
    {
        $this->attributes = $_attributes;
    }

    public function all(): array
    {
        return $this->attributes;
    }

    public function getByName(string $key): ?string
    {
        if (!array_key_exists($key, $this->attributes)) return null;

        return $this->attributes[$key];
    }

    public function add(string $key, string $value)
    {
        $this->attributes[$key] = $value;
    }

    public function remove(string $key)
    {
        if (!array_key_exists($key, $this->attributes)) return null;
        
        unset($this->attributes[$key]);
    }

    public function toArray(): array
    {
        return [
            'attributes' => $this->attributes
        ];
    }
}
