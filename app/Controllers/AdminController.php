<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ContactRepository;
use App\Repositories\UserRepository;
use mysqli;

final class AdminController
{
    private UserRepository $users;
    private ContactRepository $contacts;

    public function __construct(private mysqli $conn)
    {
        $this->users = new UserRepository($conn);
        $this->contacts = new ContactRepository($conn);
    }

    public function hideContact(int $id): bool
    {
        return $this->contacts->setHidden($id, true);
    }

    public function unhideContact(int $id): bool
    {
        return $this->contacts->setHidden($id, false);
    }

    public function deleteContact(int $id): bool
    {
        return $this->contacts->softDelete($id);
    }

    public function disableUser(int $id): bool
    {
        return $this->users->setDisabled($id, true);
    }

    public function enableUser(int $id): bool
    {
        return $this->users->setDisabled($id, false);
    }

    public function deleteUser(int $id): bool
    {
        return $this->users->softDelete($id);
    }
}
