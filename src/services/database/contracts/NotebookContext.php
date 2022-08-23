<?php
namespace App\Services\Database\Contracts;

use App\Models\User;

interface NotebookContext{
    public function addUser(User $user);
    public function deleteUser(int $id): bool;
    public function receiveUser(int $id): User|bool;
    public function updateUser(User $user);
    public function receiveUserPage(int $page);
    public function receiveAllUsers();
}