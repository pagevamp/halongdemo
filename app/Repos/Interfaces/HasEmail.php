<?php

namespace App\Repos\Interfaces;

interface HasEmail
{
    public function getEmail(): string;

    public function getName(): string;
}
