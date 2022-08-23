<?php
namespace App\Services\Contracts;

interface ImageSaver{
    public function save($image): string|bool;
    public function get(string $name);
    public function remove(string $name): bool;
}