<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait Encryptable
{
    public function getAttribute($key)
    {
        if (in_array($key, $this->encryptable)) {
            return Crypt::decryptString($this->attributes[$key]);
        }

        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable)) {
            $this->attributes[$key] = Crypt::encryptString($value);
        } else {
            parent::setAttribute($key, $value);
        }

        return $this;
    }
}