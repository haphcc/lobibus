<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Role;
use App\Models\User;

final class UserController extends AdminController
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function index(): void
    {
        $this->view('admin.users.index', [
            'title' => 'Quan ly nguoi dung',
            'users' => $this->users->allWithRoles(),
        ], 'admin');
    }

    public function create(): void
    {
        $this->view('admin.users.create', [
            'title' => 'Them nguoi dung',
            'roles' => (new Role())->all(),
        ], 'admin');
    }

    public function store(): void
    {
        if ($error = $this->requireFields([
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
        ])) {
            $this->redirect('/admin/users/create', 'error', $error);
        }

        $this->users->create($_POST);
        $this->redirect('/admin/users', 'success', 'User created.');
    }

    public function edit(): void
    {
        $user = $this->users->find($this->queryInt('id'));
        if ($user === null) {
            $this->redirect('/admin/users', 'error', 'User not found.');
        }

        $this->view('admin.users.edit', [
            'title' => 'Sua nguoi dung',
            'user' => $user,
            'roles' => (new Role())->all(),
        ], 'admin');
    }

    public function update(): void
    {
        $id = $this->postInt('id');
        if ($error = $this->requireFields([
            'name' => 'Name',
            'email' => 'Email',
        ])) {
            $this->redirect('/admin/users/edit?id=' . $id, 'error', $error);
        }

        $this->users->update($id, $_POST);
        $this->redirect('/admin/users', 'success', 'User updated.');
    }

    public function delete(): void
    {
        $this->users->lock($this->postInt('id'));
        $this->redirect('/admin/users', 'success', 'User locked.');
    }
}
