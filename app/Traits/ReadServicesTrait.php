<?php

namespace App\Traits;

use App\Models\Team;
use Illuminate\Support\Facades\Schema;

trait ReadServicesTrait
{
    /**
     * @return array|void
     */
    public function getSortParamsFromRequest()
    {
        if (is_string($this->request->__order_by) && str_contains($this->request->__order_by, ',')) {
            $sortParams = explode(',', $this->request->__order_by);
        } else if (is_array($this->request->__order_by)) {
            $sortParams = $this->request->__order_by;
        } else {
            $sortParams = [$this->request->__order_by];
        }

        foreach ($sortParams as $orderBy) {
            $field = str_replace('-', '', $orderBy);
            $direction = str_starts_with($orderBy, '-') ? 'DESC' : 'ASC';
            if (in_array($field, Schema::getColumnListing($this->serviceModelInstance->getTable()))) {
                return compact('field', 'direction');
            }
        }
    }
}
