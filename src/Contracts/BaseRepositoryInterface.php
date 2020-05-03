<?php

namespace App\Contracts;

interface BaseRepositoryInterface
{
    public function save($object): void;

    public function remove($object): void;

    public function update(): void;
}