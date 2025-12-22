<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaongkirService
{
    protected $apiKey;

    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.rajaongkir.key');
        $this->baseUrl = 'https://rajaongkir.komerce.id/api/v1';
    }

    /**
     * Get list of provinces
     *
     * @return array
     */
    public function getProvinces()
    {
        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/destination/province");

            $data = $response->json();

            if (isset($data['meta']['status']) && $data['meta']['status'] == 'success') {
                return $data['data'] ?? [];
            }

            // Log the error response for debugging
            Log::error('Rajaongkir API Error - Get Provinces: '.json_encode($data));

            return [];
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Get Provinces: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Get list of cities by province ID
     *
     * @param  int  $provinceId
     * @return array
     */
    public function getCities($provinceId)
    {
        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/destination/city/{$provinceId}");

            $data = $response->json();

            if (isset($data['meta']['status']) && $data['meta']['status'] == 'success') {
                return $data['data'] ?? [];
            }

            // Log the error response for debugging
            Log::error('Rajaongkir API Error - Get Cities: '.json_encode($data));

            return [];
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Get Cities: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Get list of districts by city ID
     *
     * @param  int  $cityId
     * @return array
     */
    public function getDistricts($cityId)
    {
        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/destination/district/{$cityId}");

            $data = $response->json();

            if (isset($data['meta']['status']) && $data['meta']['status'] == 'success') {
                return $data['data'] ?? [];
            }

            // Log the error response for debugging
            Log::error('Rajaongkir API Error - Get Districts: '.json_encode($data));

            return [];
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Get Districts: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Calculate shipping cost by district
     *
     * @param  array  $params
     * @return array
     */
    public function calculateShippingCost($params)
    {
        try {
            // Log the exact request being sent
            Log::info('RajaOngkir API Request', [
                'endpoint' => "{$this->baseUrl}/calculate/district/domestic-cost",
                'params' => $params,
            ]);

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'key' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post("{$this->baseUrl}/calculate/district/domestic-cost", $params);

            $data = $response->json();

            // Log the full response
            Log::info('RajaOngkir API Full Response', [
                'status_code' => $response->status(),
                'response' => $data,
            ]);

            if (isset($data['meta']['status']) && $data['meta']['status'] == 'success') {
                return $data['data'] ?? [];
            }

            // Log the error response for debugging
            Log::error('Rajaongkir API Error - Calculate Cost: '.json_encode($data));

            return [];
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Calculate Cost: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Get province by ID
     *
     * @param  int  $provinceId
     * @return array|null
     */
    public function getProvince($provinceId)
    {
        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/destination/province/{$provinceId}");

            $data = $response->json();

            if (isset($data['meta']['status']) && $data['meta']['status'] == 'success') {
                return $data['data'] ?? null;
            }

            // Log the error response for debugging
            Log::error('Rajaongkir API Error - Get Province: '.json_encode($data));

            return null;
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Get Province: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Get city by ID
     *
     * @param  int  $cityId
     * @return array|null
     */
    public function getCity($cityId)
    {
        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/destination/city/{$cityId}");

            $data = $response->json();

            if (isset($data['meta']['status']) && $data['meta']['status'] == 'success') {
                return $data['data'] ?? null;
            }

            // Log the error response for debugging
            Log::error('Rajaongkir API Error - Get City: '.json_encode($data));

            return null;
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Get City: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Get district by ID
     *
     * @param  int  $districtId
     * @return array|null
     */
    public function getDistrict($districtId)
    {
        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/destination/district/{$districtId}");

            $data = $response->json();

            if (isset($data['meta']['status']) && $data['meta']['status'] == 'success') {
                return $data['data'] ?? null;
            }

            // Log the error response for debugging
            Log::error('Rajaongkir API Error - Get District: '.json_encode($data));

            return null;
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Get District: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Get list of subdistricts by district ID
     *
     * @param  int  $districtId
     * @return array
     */
    public function getSubdistricts($districtId)
    {
        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'key' => $this->apiKey,
            ])->get("{$this->baseUrl}/destination/sub-district/{$districtId}");

            $data = $response->json();

            if (isset($data['meta']['status']) && $data['meta']['status'] == 'success') {
                return $data['data'] ?? [];
            }

            // Log the error response for debugging
            Log::error('Rajaongkir API Error - Get Subdistricts: '.json_encode($data));

            return [];
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Get Subdistricts: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Get subdistrict by ID
     *
     * @param  int  $subdistrictId
     * @return array|null
     */
    public function getSubdistrict($subdistrictId)
    {
        // Note: RajaOngkir doesn't have endpoint to get single subdistrict by ID
        // We need to get the parent district first, then find the subdistrict in the list
        // For now, we'll return a simple structure
        // In production, you might want to cache the district->subdistrict mapping

        try {
            // Since we don't have the district ID, we'll return a basic structure
            // The name will be fetched when needed from the subdistricts array
            return [
                'id' => $subdistrictId,
                'name' => '', // Will be populated from the dropdown selection
            ];
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Get Subdistrict: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Calculate shipping cost by district or subdistrict
     *
     * RajaOngkir API supports BOTH district-level and subdistrict-level calculation
     * using the same endpoint. Simply pass subdistrict IDs in the origin/destination
     * parameters for kelurahan-level precision.
     *
     * @param  array  $params  Parameters should include:
     *                         - origin: district_id or subdistrict_id
     *                         - destination: district_id or subdistrict_id
     *                         - weight: package weight in grams
     *                         - courier: courier codes separated by colon
     * @return array
     */
    public function calculateShippingCostBySubdistrict($params)
    {
        try {
            // Determine if we're using district or subdistrict IDs for logging
            $locationType = (isset($params['_use_subdistrict']) && $params['_use_subdistrict'])
                ? 'Subdistrict'
                : 'District';

            // Remove internal flags before sending to API
            unset($params['_use_subdistrict']);

            Log::info("RajaOngkir API Request ({$locationType} Level)", [
                'endpoint' => "{$this->baseUrl}/calculate/district/domestic-cost",
                'params' => $params,
            ]);

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'key' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post("{$this->baseUrl}/calculate/district/domestic-cost", $params);

            $data = $response->json();

            Log::info("RajaOngkir API Full Response ({$locationType} Level)", [
                'status_code' => $response->status(),
                'response' => $data,
            ]);

            if (isset($data['meta']['status']) && $data['meta']['status'] == 'success') {
                return $data['data'] ?? [];
            }

            Log::error("Rajaongkir API Error - Calculate Cost ({$locationType}): ".json_encode($data));

            return [];
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Calculate Cost: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Calculate domestic shipping cost using search-based endpoint
     * Supports both district and subdistrict level calculation
     *
     * @param  array  $params
     * @return array
     */
    public function calculateDomesticCost($params)
    {
        try {
            Log::info('RajaOngkir API Request (Search-Based)', [
                'endpoint' => "{$this->baseUrl}/calculate/domestic-cost",
                'params' => $params,
            ]);

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'key' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post("{$this->baseUrl}/calculate/domestic-cost", $params);

            $data = $response->json();

            Log::info('RajaOngkir API Full Response (Search-Based)', [
                'status_code' => $response->status(),
                'response' => $data,
            ]);

            if (isset($data['meta']['status']) && $data['meta']['status'] == 'success') {
                return $data['data'] ?? [];
            }

            Log::error('Rajaongkir API Error - Calculate Domestic Cost: '.json_encode($data));

            return [];
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Calculate Domestic Cost: '.$e->getMessage());

            return [];
        }
    }
}
