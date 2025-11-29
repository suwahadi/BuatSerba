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
            Log::error('Rajaongkir API Error - Get Provinces: ' . json_encode($data));
            return [];
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Get Provinces: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get list of cities by province ID
     *
     * @param int $provinceId
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
            Log::error('Rajaongkir API Error - Get Cities: ' . json_encode($data));
            return [];
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Get Cities: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get list of districts by city ID
     *
     * @param int $cityId
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
            Log::error('Rajaongkir API Error - Get Districts: ' . json_encode($data));
            return [];
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Get Districts: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate shipping cost by district
     *
     * @param array $params
     * @return array
     */
    public function calculateShippingCost($params)
    {
        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'key' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post("{$this->baseUrl}/calculate/district/domestic-cost", $params);

            $data = $response->json();
            
            if (isset($data['meta']['status']) && $data['meta']['status'] == 'success') {
                return $data['data'] ?? [];
            }

            // Log the error response for debugging
            Log::error('Rajaongkir API Error - Calculate Cost: ' . json_encode($data));
            return [];
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Calculate Cost: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get province by ID
     *
     * @param int $provinceId
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
            Log::error('Rajaongkir API Error - Get Province: ' . json_encode($data));
            return null;
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Get Province: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get city by ID
     *
     * @param int $cityId
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
            Log::error('Rajaongkir API Error - Get City: ' . json_encode($data));
            return null;
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Get City: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get district by ID
     *
     * @param int $districtId
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
            Log::error('Rajaongkir API Error - Get District: ' . json_encode($data));
            return null;
        } catch (\Exception $e) {
            Log::error('Rajaongkir API Error - Get District: ' . $e->getMessage());
            return null;
        }
    }
}