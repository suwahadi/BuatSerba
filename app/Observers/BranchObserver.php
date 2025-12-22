<?php

namespace App\Observers;

use App\Models\Branch;
use App\Services\RajaongkirService;

class BranchObserver
{
    /**
     * Handle the Branch "saving" event.
     * Update location names before saving.
     */
    public function saving(Branch $branch): void
    {
        $rajaongkir = new RajaongkirService;

        // Update province_name if province_id exists
        if ($branch->province_id && $branch->isDirty('province_id')) {
            $provinces = $rajaongkir->getProvinces();
            $province = collect($provinces)->firstWhere('id', $branch->province_id);
            if ($province) {
                $branch->province_name = $province['name'];
            }
        }

        // Update city_name if city_id exists
        if ($branch->city_id && ($branch->isDirty('city_id') || $branch->isDirty('province_id'))) {
            $cities = $rajaongkir->getCities($branch->province_id);
            $city = collect($cities)->firstWhere('id', $branch->city_id);
            if ($city) {
                $type = isset($city['type']) ? trim($city['type']) : '';
                $branch->city_name = $type ? "{$type} {$city['name']}" : $city['name'];
            }
        }

        // Update district_name if district_id exists
        if ($branch->district_id && ($branch->isDirty('district_id') || $branch->isDirty('city_id'))) {
            $districts = $rajaongkir->getDistricts($branch->city_id);
            $district = collect($districts)->firstWhere('id', $branch->district_id);
            if ($district) {
                $branch->district_name = $district['name'];
            }
        }

        // Update subdistrict_name if subdistrict_id exists
        if ($branch->subdistrict_id && ($branch->isDirty('subdistrict_id') || $branch->isDirty('district_id'))) {
            $subdistricts = $rajaongkir->getSubdistricts($branch->district_id);
            $subdistrict = collect($subdistricts)->firstWhere('id', $branch->subdistrict_id);
            if ($subdistrict) {
                $branch->subdistrict_name = $subdistrict['name'];
            }
        }
    }
}
