<?php

namespace App\Service\Interfaces;

interface CrypticInterface
{
    public function encrypt(string $data): string;
    public function decrypt(string $data): string;
}