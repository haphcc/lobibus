<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Location;

final class LocationController extends AdminController
{
    private Location $locations;

    public function __construct()
    {
        $this->locations = new Location();
    }

    public function index(): void
    {
        $this->view('admin.locations.index', [
            'title' => 'Quan ly dia diem',
            'locations' => $this->locations->all(),
        ], 'admin');
    }

    public function create(): void
    {
        $this->view('admin.locations.create', ['title' => 'Them dia diem'], 'admin');
    }

    public function store(): void
    {
        if ($error = $this->requireFields(['name' => 'Name'])) {
            $this->redirect('/admin/locations/create', 'error', $error);
        }

        $this->locations->create($_POST);
        $this->redirect('/admin/locations', 'success', 'Location created.');
    }

    public function edit(): void
    {
        $location = $this->locations->find($this->queryInt('id'));
        if ($location === null) {
            $this->redirect('/admin/locations', 'error', 'Location not found.');
        }

        $this->view('admin.locations.edit', [
            'title' => 'Sua dia diem',
            'location' => $location,
        ], 'admin');
    }

    public function update(): void
    {
        $id = $this->postInt('id');
        if ($error = $this->requireFields(['name' => 'Name'])) {
            $this->redirect('/admin/locations/edit?id=' . $id, 'error', $error);
        }

        $this->locations->update($id, $_POST);
        $this->redirect('/admin/locations', 'success', 'Location updated.');
    }

    public function delete(): void
    {
        $id = $this->postInt('id');
        if ($this->locations->isUsed($id)) {
            $this->redirect('/admin/locations', 'error', 'Cannot delete a location used by routes.');
        }

        $this->locations->delete($id);
        $this->redirect('/admin/locations', 'success', 'Location deleted.');
    }
}
