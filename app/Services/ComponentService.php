<?php

namespace App\Services;

use App\Models\Component;

class ComponentService
{
    public function list()
    {
        return Component::latest()->get();
    }

    public function store(array $data)
    {
        return Component::create($data);
    }

    public function update(Component $component, array $data)
    {
        $component->update($data);
        return $component;
    }

    public function delete(Component $component)
    {
        $component->delete();
    }
}
