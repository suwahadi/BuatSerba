<?php

namespace App\Livewire\Dashboard;

use App\Models\UserAddress;
use App\Services\RajaongkirService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Alamat Saya - BuatSerba')]
class Address extends Component
{
    public $provinceId = '';

    public $cityId = '';

    public $districtId = '';

    public $subdistrictId = '';

    public $postalCode = '';

    public $fullAddress = '';

    // Available regions
    public $provinces = [];

    public $cities = [];

    public $districts = [];

    public $subdistricts = [];

    public $hasExistingAddress = false;

    public $addressId = null;

    public function mount()
    {
        // Load provinces from RajaOngkir API
        $rajaongkir = new RajaongkirService;
        $provincesData = $rajaongkir->getProvinces();

        $this->provinces = collect($provincesData)->mapWithKeys(function ($province) {
            return [$province['id'] => $province['name']];
        })->toArray();

        // Load existing address if available
        $address = UserAddress::where('user_id', auth()->id())->first();

        if ($address) {
            $this->hasExistingAddress = true;
            $this->addressId = $address->id;
            $this->provinceId = $address->province_id;
            $this->cityId = $address->city_id;
            $this->districtId = $address->district_id;
            $this->subdistrictId = $address->subdistrict_id ?? '';
            $this->postalCode = $address->postal_code;
            $this->fullAddress = $address->full_address;

            // Load cities for the selected province
            if ($this->provinceId) {
                $citiesData = $rajaongkir->getCities($this->provinceId);
                $this->cities = collect($citiesData)->mapWithKeys(function ($city) {
                    $type = $city['type'] ?? '';
                    $name = $city['name'] ?? '';
                    $displayName = trim($type.' '.$name);

                    return [$city['id'] => $displayName];
                })->toArray();
            }

            // Load districts for the selected city
            if ($this->cityId) {
                $districtsData = $rajaongkir->getDistricts($this->cityId);
                $this->districts = collect($districtsData)->mapWithKeys(function ($district) {
                    return [$district['id'] => $district['name']];
                })->toArray();
            }

            // Load subdistricts for the selected district
            if ($this->districtId) {
                $subdistrictsData = $rajaongkir->getSubdistricts($this->districtId);
                $this->subdistricts = collect($subdistrictsData)->mapWithKeys(function ($subdistrict) {
                    return [$subdistrict['id'] => $subdistrict['name']];
                })->toArray();
            }
        }
    }

    public function updatedProvinceId($provinceId)
    {
        $this->cityId = '';
        $this->districtId = '';
        $this->subdistrictId = '';
        $this->cities = [];
        $this->districts = [];
        $this->subdistricts = [];

        if ($provinceId) {
            $rajaongkir = new RajaongkirService;
            $citiesData = $rajaongkir->getCities($provinceId);

            $this->cities = collect($citiesData)->mapWithKeys(function ($city) {
                $type = $city['type'] ?? '';
                $name = $city['name'] ?? '';
                $displayName = trim($type.' '.$name);

                return [$city['id'] => $displayName];
            })->toArray();
        }
    }

    public function updatedCityId($cityId)
    {
        $this->districtId = '';
        $this->subdistrictId = '';
        $this->districts = [];
        $this->subdistricts = [];

        if ($cityId) {
            $rajaongkir = new RajaongkirService;
            $districtsData = $rajaongkir->getDistricts($cityId);

            $this->districts = collect($districtsData)->mapWithKeys(function ($district) {
                return [$district['id'] => $district['name']];
            })->toArray();
        }
    }

    public function updatedDistrictId($districtId)
    {
        $this->subdistrictId = '';
        $this->subdistricts = [];

        if ($districtId) {
            $rajaongkir = new RajaongkirService;
            $subdistrictsData = $rajaongkir->getSubdistricts($districtId);

            $this->subdistricts = collect($subdistrictsData)->mapWithKeys(function ($subdistrict) {
                return [$subdistrict['id'] => $subdistrict['name']];
            })->toArray();
        }
    }

    protected function rules(): array
    {
        return [
            'provinceId' => 'required',
            'cityId' => 'required',
            'districtId' => 'required',
            'postalCode' => 'required|min:5|max:5',
            'fullAddress' => 'required|min:10|max:500',
        ];
    }

    protected $messages = [
        'provinceId.required' => 'Pilih provinsi terlebih dahulu',
        'cityId.required' => 'Pilih kota/kabupaten terlebih dahulu',
        'districtId.required' => 'Pilih kecamatan terlebih dahulu',
        'postalCode.required' => 'Kode pos harus diisi',
        'postalCode.min' => 'Kode pos harus 5 digit',
        'postalCode.max' => 'Kode pos harus 5 digit',
        'fullAddress.required' => 'Alamat lengkap harus diisi',
        'fullAddress.min' => 'Alamat lengkap minimal 10 karakter',
        'fullAddress.max' => 'Alamat lengkap maksimal 500 karakter',
    ];

    public function saveAddress()
    {
        $this->validate();

        try {
            $provinceName = strtoupper($this->provinces[$this->provinceId] ?? '');
            $cityName = strtoupper($this->cities[$this->cityId] ?? '');

            // Extract city type and name
            $cityType = '';
            $cityNameOnly = $cityName;
            if (strpos($cityName, 'KABUPATEN ') === 0) {
                $cityType = 'Kabupaten';
                $cityNameOnly = str_replace('KABUPATEN ', '', $cityName);
            } elseif (strpos($cityName, 'KOTA ') === 0) {
                $cityType = 'Kota';
                $cityNameOnly = str_replace('KOTA ', '', $cityName);
            }

            $districtName = strtoupper($this->districts[$this->districtId] ?? '');
            $subdistrictName = ! empty($this->subdistrictId) && isset($this->subdistricts[$this->subdistrictId])
                ? strtoupper($this->subdistricts[$this->subdistrictId])
                : null;

            $data = [
                'user_id' => auth()->id(),
                'province_id' => $this->provinceId,
                'province_name' => $provinceName,
                'city_id' => $this->cityId,
                'city_name' => $cityNameOnly,
                'city_type' => $cityType,
                'district_id' => $this->districtId,
                'district_name' => $districtName,
                'subdistrict_id' => $this->subdistrictId ?: null,
                'subdistrict_name' => $subdistrictName,
                'postal_code' => $this->postalCode,
                'full_address' => $this->fullAddress,
                'is_primary' => true,
            ];

            if ($this->hasExistingAddress && $this->addressId) {
                // Update existing address
                UserAddress::where('id', $this->addressId)->update($data);
                $message = 'Alamat berhasil diperbarui!';
            } else {
                // Create new address
                UserAddress::create($data);
                $message = 'Alamat berhasil disimpan!';
                $this->hasExistingAddress = true;
            }

            $this->dispatch('notify-success', message: $message);

        } catch (\Exception $e) {
            $this->dispatch('notify-error', message: 'Gagal menyimpan alamat: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.dashboard.address');
    }
}
