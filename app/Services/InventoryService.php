<?php

namespace App\Services;

use App\Models\Component;

class InventoryService
{
    /**
     * Fetch all active components for Current Stock
     */
    public function getCurrentStock()
    {
        return Component::where('status', 1)
            ->orderBy('component_name')
            ->get();
    }

    /**
     * Fetch only low-stock components
     */
    public function getLowStock()
    {
        return Component::where('status', 1)
            ->whereColumn('current_stock', '<', 'minimum_stock')
            ->orderBy('component_name')
            ->get();
    }
}
