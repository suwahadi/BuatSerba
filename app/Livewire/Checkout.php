<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\CartItem;
use App\Services\MidtransService;
use App\Services\OrderService;
use App\Services\RajaongkirService;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Checkout extends Component
{
    public $fullName = '';

    public $email = '';

    public $phone = '';

    public $provinceId = '';

    public $cityId = '';

    public $districtId = '';

    public $subdistrictId = '';

    public $postalCode = '';

    public $address = '';

    public $provinces = [];

    public $cities = [];

    public $districts = [];

    public $subdistricts = [];

    public $districtName = '';

    public $subdistrictName = '';

    public $shippingMethod = '';

    public $shippingCost = 0;

    public $paymentMethod = '';

    public $serviceFee = 0;

    public $discount = 0;

    public $voucherCode = '';

    public $shippingMethods = [];

    public $branches = [];

    public $selectedBranchId = null;

    public $showBranchModal = true;

    #[Computed]
    public function paymentMethods()
    {
        return [
            // [
            //     'id' => 'bank-transfer-bca',
            //     'name' => 'BCA Virtual Account',
            //     'description' => 'Transfer ke rekening virtual BCA',
            //     'icon' => 'bank',
            // ],
            [
                'id' => 'bank-transfer-mandiri',
                'name' => 'Mandiri Virtual Account',
                'description' => 'Transfer ke rekening virtual Mandiri',
                'icon' => 'bank',
            ],
            [
                'id' => 'bank-transfer-bni',
                'name' => 'BNI Virtual Account',
                'description' => 'Transfer ke rekening virtual BNI',
                'icon' => 'bank',
            ],
            [
                'id' => 'bank-transfer-bri',
                'name' => 'BRI Virtual Account',
                'description' => 'Transfer ke rekening virtual BRI',
                'icon' => 'bank',
            ],
            // [
            //     'id' => 'bank-transfer-permata',
            //     'name' => 'Permata Virtual Account',
            //     'description' => 'Transfer ke rekening virtual Permata',
            //     'icon' => 'bank',
            // ],
            [
                'id' => 'bank-transfer',
                'name' => 'Bank Transfer ('.(global_config('manual_bank_name') ?? 'BCA').')',
                'description' => 'Transfer manual ke rekening '.(global_config('manual_bank_name') ?? 'BCA'),
                'icon' => 'bank',
            ],
        ];
    }

    #[Computed]
    public function activeBranches()
    {
        return Branch::where('is_active', true)->orderBy('priority')->get()->toArray();
    }

    #[Computed]
    public function selectedBranch()
    {
        if ($this->selectedBranchId) {
            return Branch::find($this->selectedBranchId);
        }

        return Branch::where('is_active', true)->orderBy('priority')->first();
    }

    #[Locked]
    protected $rules = [
        'fullName' => 'required|min:3',
        'email' => 'required|email',
        'phone' => 'required|min:10',
        'provinceId' => 'required',
        'cityId' => 'required',
        'districtId' => 'required',
        'postalCode' => 'required',
        'address' => 'required|min:10',
        'shippingMethod' => 'required',
        'paymentMethod' => 'required',
    ];

    protected $messages = [
        'fullName.required' => 'Nama lengkap harus diisi',
        'fullName.min' => 'Nama lengkap harus diisi minimal 3 karakter',
        'email.required' => 'Email harus diisi',
        'email.email' => 'Email harus diisi dengan format yang benar',
        'phone.required' => 'Nomor telepon harus diisi',
        'phone.min' => 'Nomor telepon harus diisi minimal 10 digit',
        'provinceId.required' => 'Pilih provinsi terlebih dahulu',
        'cityId.required' => 'Pilih kota/kabupaten terlebih dahulu',
        'districtId.required' => 'Pilih kecamatan terlebih dahulu',
        'postalCode.required' => 'Kode pos harus diisi',
        'address.required' => 'Alamat lengkap harus diisi',
        'address.min' => 'Alamat lengkap harus diisi minimal 10 karakter',
        'shippingMethod.required' => 'Pilih metode pengiriman terlebih dahulu',
        'paymentMethod.required' => 'Pilih metode pembayaran terlebih dahulu',
    ];

    public function mount()
    {

        $rajaongkir = new RajaongkirService;
        $provincesData = $rajaongkir->getProvinces();

        $this->provinces = collect($provincesData)->mapWithKeys(function ($province) {
            return [$province['id'] => $province['name']];
        })->toArray();

        $this->branches = Branch::where('is_active', true)->orderBy('priority')->get()->toArray();

        $this->shippingMethods = [];

        if ($this->cartItems->isEmpty()) {
            session()->flash('error', 'Keranjang belanja Anda kosong.');

            return redirect()->route('cart');
        }

        if (count($this->branches) === 1) {
            $this->selectedBranchId = $this->branches[0]['id'];
            $this->showBranchModal = false;
        } else {
            $this->showBranchModal = true;
        }

        if (Session::has('applied_voucher')) {
            $voucherData = Session::get('applied_voucher');
            $this->voucherCode = $voucherData['code'];

            $voucherService = new \App\Services\VoucherService;
            $result = $voucherService->applyVoucher(
                $this->voucherCode,
                $this->subtotal,
                auth()->user()
            );

            if ($result['success']) {
                $this->discount = $result['data']['discount_amount'];
                Session::put('applied_voucher', $result['data']);
            } else {
                $this->discount = 0;
                $this->voucherCode = '';
                Session::forget('applied_voucher');
            }
        }

        if (auth()->check()) {
            $user = auth()->user();

            $this->fullName = $user->name ?? '';
            $this->email = $user->email ?? '';
            $this->phone = $user->phone ?? '';

            $address = $user->addresses()->where('is_primary', true)->first()
                    ?? $user->addresses()->first();

            if ($address) {

                $this->provinceId = $address->province_id;
                $this->cityId = $address->city_id;
                $this->districtId = $address->district_id;
                $this->subdistrictId = $address->subdistrict_id ?? '';
                $this->postalCode = $address->postal_code;
                $this->address = $address->full_address;

                if ($this->provinceId) {
                    $citiesData = $rajaongkir->getCities($this->provinceId);
                    $this->cities = collect($citiesData)->mapWithKeys(function ($city) {
                        $type = $city['type'] ?? '';
                        $name = $city['name'] ?? '';
                        $displayName = trim($type.' '.$name);

                        return [$city['id'] => $displayName];
                    })->toArray();
                }

                if ($this->cityId) {
                    $districtsData = $rajaongkir->getDistricts($this->cityId);
                    $this->districts = collect($districtsData)->mapWithKeys(function ($district) {
                        return [$district['id'] => $district['name']];
                    })->toArray();

                    if ($this->districtId && isset($this->districts[$this->districtId])) {
                        $this->districtName = $this->districts[$this->districtId];
                    }
                }

                if ($this->districtId) {
                    $subdistrictsData = $rajaongkir->getSubdistricts($this->districtId);
                    $this->subdistricts = collect($subdistrictsData)->mapWithKeys(function ($subdistrict) {
                        return [$subdistrict['id'] => $subdistrict['name']];
                    })->toArray();

                    if ($this->subdistrictId && isset($this->subdistricts[$this->subdistrictId])) {
                        $this->subdistrictName = $this->subdistricts[$this->subdistrictId];
                    }
                }

                if ($this->selectedBranchId && $this->districtId) {
                    $this->calculateShippingCost();
                }
            }
        }

    }

    public function selectBranch($branchId)
    {
        $this->selectedBranchId = $branchId;
        $this->showBranchModal = false;

        if ($this->subdistrictId) {
            $this->calculateShippingCost();
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
        $this->shippingMethods = [];

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
        $this->shippingMethods = [];

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
        $this->shippingMethods = [];

        if ($districtId && isset($this->districts[$districtId])) {
            $this->districtName = $this->districts[$districtId];
        }

        if ($districtId) {

            $rajaongkir = new RajaongkirService;
            $subdistrictsData = $rajaongkir->getSubdistricts($districtId);

            $this->subdistricts = collect($subdistrictsData)->mapWithKeys(function ($subdistrict) {
                return [$subdistrict['id'] => $subdistrict['name']];
            })->toArray();
        }
    }

    public function updatedSubdistrictId($subdistrictId)
    {

        if ($subdistrictId && isset($this->subdistricts[$subdistrictId])) {
            $this->subdistrictName = $this->subdistricts[$subdistrictId];
        }

        $this->calculateShippingCost();
    }

    #[Computed]
    public function cartItems()
    {
        $sessionId = Session::get('cart_session_id');

        return CartItem::with(['product', 'sku'])
            ->where('session_id', $sessionId)
            ->when(auth()->check(), function ($query) {
                $query->orWhere('user_id', auth()->id());
            })
            ->get();
    }

    #[Computed]
    public function subtotal()
    {
        return $this->cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    #[Computed]
    public function total()
    {
        return $this->subtotal + $this->shippingCost + $this->serviceFee - $this->discount;
    }

    public function updatedShippingMethod($value)
    {
        $this->updateShippingCost();
    }

    public function calculateShippingCost()
    {

        if (! $this->districtId || ! $this->selectedBranch) {

            $this->shippingMethods = [];

            return;
        }

        try {

            $rajaongkir = new RajaongkirService;

            $totalWeight = $this->cartItems->sum(function ($item) {
                return ($item->sku->weight ?? 1000) * $item->quantity;
            });

            $originId = $this->selectedBranch->subdistrict_id;
            $destinationId = $this->subdistrictId ?: $this->districtId;
            $useSubdistrict = ! empty($this->subdistrictId);

            if (! $originId) {

                $this->shippingMethods = [];

                return;
            }

            $locationType = $useSubdistrict ? 'subdistrict' : 'district';
            $destinationName = $useSubdistrict
                ? ($this->subdistrictName ?: 'Unknown Subdistrict')
                : ($this->districts[$this->districtId] ?? 'Unknown District');

            $params = [
                'origin' => $originId,
                'destination' => $destinationId,
                'weight' => max(200, $totalWeight),
                'courier' => 'jne:jnt',
                'price' => 'lowest',
                '_use_subdistrict' => $useSubdistrict,
            ];

            $shippingResults = $rajaongkir->calculateDomesticCost($params);
            $this->processShippingResults($shippingResults);

        } catch (\Exception $e) {

            $this->shippingMethods = [];
        }
    }

    protected function processShippingResults($results)
    {
        $shippingOptions = [];

        if (empty($results)) {
            $this->shippingMethods = [];

            return;
        }

        foreach ($results as $result) {
            $courier = $result['code'];
            $courierName = $result['name'];
            $service = $result['service'];
            $description = $result['description'];
            $cost = $result['cost'];
            $etd = $result['etd'];

            $shippingOptions[] = [
                'id' => strtolower($courier).'_'.strtolower(str_replace(' ', '_', $service)),
                'name' => $this->formatShippingName($courier, $service),
                'description' => $description,
                'cost' => $cost,
                'estimatedDays' => $etd ?? '',
            ];
        }

        if (! empty($shippingOptions)) {

            $this->shippingMethods = array_map(function ($option) {
                return [
                    'id' => (string) $option['id'],
                    'name' => (string) $option['name'],
                    'description' => (string) $option['description'],
                    'cost' => (int) $option['cost'],
                    'estimatedDays' => (string) $option['estimatedDays'],
                ];
            }, $shippingOptions);

            // Apply Free Shipping Subsidy if applicable
            if (Session::has('applied_voucher')) {
                $voucherData = Session::get('applied_voucher');
                if (! empty($voucherData['is_free_shipment']) && isset($voucherData['amount_value'])) {
                    $subsidy = $voucherData['amount_value'];

                    $this->shippingMethods = array_map(function ($option) use ($subsidy) {
                        $option['original_cost'] = $option['cost'];
                        $option['subsidy'] = $subsidy;

                        return $option;
                    }, $this->shippingMethods);

                    $this->discount = 0;
                }
            }

            $this->shippingMethod = $this->shippingMethods[0]['id'] ?? 'regular';
        } else {

            $this->shippingMethods = [];
        }

        $this->updateShippingCost();
    }

    protected function updateShippingCost()
    {
        $method = collect($this->shippingMethods)->firstWhere('id', $this->shippingMethod);

        if ($method) {
            $originalCost = $method['original_cost'] ?? $method['cost'];
            $subsidy = $method['subsidy'] ?? 0;

            $this->shippingCost = max(0, $originalCost - $subsidy);
        }
    }

    #[Computed]
    public function selectedShippingOriginalCost()
    {
        $method = collect($this->shippingMethods)->firstWhere('id', $this->shippingMethod);
        if (! $method) {
            return 0;
        }

        return $method['original_cost'] ?? $method['cost'];
    }

    protected function initializeDefaultShippingMethods() {}

    public function selectNearestBranch() {}

    public function placeOrder()
    {

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('validation-failed');
            throw $e;
        }

        if ($this->cartItems->isEmpty()) {
            $this->dispatch('showModalError', [
                'title' => 'Keranjang Kosong',
                'message' => 'Keranjang belanja Anda kosong. Silakan tambahkan produk terlebih dahulu.',
            ]);

            return;
        }

        try {

            $provinceName = strtoupper($this->provinces[$this->provinceId] ?? '');
            $cityName = strtoupper($this->cities[$this->cityId] ?? '');
            $districtName = strtoupper($this->districts[$this->districtId] ?? '');
            $subdistrictName = strtoupper($this->subdistricts[$this->subdistrictId] ?? '');

            $orderService = new OrderService;

            $paymentMethodParts = explode('-', $this->paymentMethod);
            $bankName = end($paymentMethodParts);

            $order = $orderService->createOrder([
                'customer_name' => $this->fullName,
                'customer_email' => $this->email,
                'customer_phone' => $this->phone,
                'shipping_province' => $provinceName,
                'shipping_city' => $cityName,
                'shipping_district' => $districtName,
                'shipping_subdistrict' => $subdistrictName,
                'shipping_postal_code' => $this->postalCode,
                'shipping_address' => $this->address,
                'shipping_method' => $this->shippingMethod,
                'shipping_cost' => $this->shippingCost,
                'payment_method' => $bankName,
                'service_fee' => $this->serviceFee,
                'discount' => $this->discount,
                'branch_id' => (int) ($this->selectedBranchId ?? 1),
            ]);

            if ($this->paymentMethod === 'bank-transfer') {
                \App\Models\Payment::create([
                    'order_id' => $order->id,
                    'payment_gateway' => 'bank_transfer',
                    'transaction_id' => (string) \Illuminate\Support\Str::uuid(),
                    'transaction_time' => now(),
                    'transaction_status' => 'pending',
                    'payment_type' => 'bank_transfer',
                    'payment_channel' => strtolower(global_config('manual_bank_name') ?? 'bca'),
                    'gross_amount' => $order->total,
                    'currency' => 'IDR',
                    'status_code' => '201',
                    'status_message' => 'Waiting for manual transfer',
                    'expired_at' => $order->payment_deadline,
                ]);

                $order->update([
                    'status' => 'pending',
                    'payment_status' => 'pending',
                ]);

            } elseif ($this->paymentMethod !== 'cod') {
                $midtransService = new MidtransService;
                $paymentResult = $midtransService->createTransaction($order, $this->paymentMethod);

                if ($paymentResult['success']) {

                } else {

                    $this->dispatch('showModalError', [
                        'title' => 'Gagal Membuat Pembayaran',
                        'message' => 'Gagal membuat pembayaran: '.$paymentResult['message'],
                    ]);

                    return;
                }
            } else {

                $order->update([
                    'status' => 'pending',
                    'payment_status' => 'pending',
                ]);
            }

            $this->dispatch('showModalSuccess', [
                'title' => 'Pesanan Berhasil Dibuat',
                'message' => 'Pesanan Anda telah berhasil dibuat. Nomor pesanan: '.$order->order_number,
            ]);

            return redirect()->route('payment', ['code' => $order->order_number]);

        } catch (\Exception $e) {

            $this->dispatch('showModalError', [
                'title' => 'Error',
                'message' => $e->getMessage(),
            ]);

            return;
        }
    }

    public function render()
    {
        return view('livewire.checkout')->layout('components.layouts.guest');
    }

    protected function formatShippingName($courierCode, $service)
    {
        $courierCode = strtolower($courierCode);

        $shortName = match ($courierCode) {
            'jne' => 'JNE',
            'jnt' => 'J&T',
            'pos' => 'POS',
            'tiki' => 'TIKI',
            'sicepat' => 'SiCepat',
            'anteraja' => 'AnterAja',
            'ninja' => 'Ninja',
            'lion' => 'Lion Parcel',
            'ide' => 'ID Express',
            default => strtoupper($courierCode),
        };

        return "{$shortName} ({$service})";
    }
}
