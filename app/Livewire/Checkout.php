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
    // Customer Information
    public $fullName = '';

    public $email = '';

    public $phone = '';

    // Shipping Address
    public $provinceId = '';

    public $cityId = '';

    public $districtId = '';

    public $subdistrictId = '';

    public $postalCode = '';

    public $address = '';

    // Available regions
    public $provinces = [];

    public $cities = [];

    public $districts = [];

    public $subdistricts = [];

    // Store subdistrict name for order
    public $subdistrictName = '';

    // Shipping Method
    public $shippingMethod = '';

    public $shippingCost = 0;

    // Payment Method
    public $paymentMethod = '';

    // Order Summary
    public $serviceFee = 2000;

    public $discount = 0;

    // Available Options (Will be populated with dynamic options)
    public $shippingMethods = [];

    // Branch Selection
    public $branches = [];

    public $selectedBranchId = null;

    public $showBranchModal = true;

    #[Computed]
    public function paymentMethods()
    {
        return [
            [
                'id' => 'bank-transfer-bca',
                'name' => 'BCA Virtual Account',
                'description' => 'Transfer ke rekening virtual BCA',
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
        ];
    }

    // Selected branch for shipping origin
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
        // Load provinces from RajaOngkir API
        $rajaongkir = new RajaongkirService;
        $provincesData = $rajaongkir->getProvinces();

        // Convert to key-value array for dropdown
        $this->provinces = collect($provincesData)->mapWithKeys(function ($province) {
            return [$province['id'] => $province['name']];
        })->toArray();

        // Load active branches for selection
        $this->branches = Branch::where('is_active', true)->orderBy('priority')->get()->toArray();

        // Initialize with empty shipping methods - will be populated when subdistrict is selected
        $this->shippingMethods = [];

        // Check if cart is empty
        if ($this->cartItems->isEmpty()) {
            session()->flash('error', 'Keranjang belanja Anda kosong.');

            return redirect()->route('cart');
        }

        // Show branch selection modal on initial load
        $this->showBranchModal = true;
    }

    public function selectBranch($branchId)
    {
        $this->selectedBranchId = $branchId;
        $this->showBranchModal = false;

        // Recalculate shipping cost with new branch origin if subdistrict is already selected
        if ($this->subdistrictId) {
            $this->calculateShippingCost();
        }
    }

    public function updatedProvinceId($provinceId)
    {
        // Reset dependent fields when province changes
        $this->cityId = '';
        $this->districtId = '';
        $this->subdistrictId = '';
        $this->cities = [];
        $this->districts = [];
        $this->subdistricts = [];
        $this->shippingMethods = [];

        if ($provinceId) {
            // Load cities from RajaOngkir API
            $rajaongkir = new RajaongkirService;
            $citiesData = $rajaongkir->getCities($provinceId);

            // Convert to key-value array for dropdown
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
        // Reset dependent fields when city changes
        $this->districtId = '';
        $this->subdistrictId = '';
        $this->districts = [];
        $this->subdistricts = [];
        $this->shippingMethods = [];

        if ($cityId) {
            // Load districts from RajaOngkir API
            $rajaongkir = new RajaongkirService;
            $districtsData = $rajaongkir->getDistricts($cityId);

            // Convert to key-value array for dropdown
            $this->districts = collect($districtsData)->mapWithKeys(function ($district) {
                return [$district['id'] => $district['name']];
            })->toArray();
        }
    }

    public function updatedDistrictId($districtId)
    {
        // Reset dependent fields when district changes
        $this->subdistrictId = '';
        $this->subdistricts = [];
        $this->shippingMethods = [];

        if ($districtId) {
            // Load subdistricts from RajaOngkir API
            $rajaongkir = new RajaongkirService;
            $subdistrictsData = $rajaongkir->getSubdistricts($districtId);

            // Convert to key-value array for dropdown
            $this->subdistricts = collect($subdistrictsData)->mapWithKeys(function ($subdistrict) {
                return [$subdistrict['id'] => $subdistrict['name']];
            })->toArray();
        }
    }

    public function updatedSubdistrictId($subdistrictId)
    {
        // Store the subdistrict name for later use
        if ($subdistrictId && isset($this->subdistricts[$subdistrictId])) {
            $this->subdistrictName = $this->subdistricts[$subdistrictId];
        }

        // Recalculate shipping when subdistrict changes
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

    protected function updateShippingCost()
    {
        // Find selected shipping method
        $method = collect($this->shippingMethods)->firstWhere('id', $this->shippingMethod);

        if ($method) {
            $this->shippingCost = $method['cost'];
        }
    }

    public function calculateShippingCost()
    {
        // Only calculate if we have district selected
        if (! $this->districtId || ! $this->selectedBranch) {
            // Clear shipping methods if district not selected
            $this->shippingMethods = [];

            return;
        }

        try {
            // Initialize Rajaongkir service
            $rajaongkir = new RajaongkirService;

            // Calculate total package weight
            $totalWeight = $this->cartItems->sum(function ($item) {
                return ($item->sku->weight ?? 1000) * $item->quantity;
            });

            // Determine origin and destination IDs
            // IMPORTANT: Both origin and destination should use the same level (both district OR both subdistrict)
            // Origin: Always use branch's subdistrict_id (branches should have this configured)
            $originId = $this->selectedBranch->subdistrict_id;

            // Destination: Prefer subdistrict if selected, otherwise use district
            // When user selects subdistrict (kelurahan), we get more accurate pricing
            $destinationId = $this->subdistrictId ?: $this->districtId;
            $useSubdistrict = ! empty($this->subdistrictId);

            if (! $originId) {
                \Log::warning('Origin subdistrict_id is null for branch: '.$this->selectedBranch->name);
                $this->shippingMethods = [];

                return;
            }

            // Prepare location type information for logging
            $locationType = $useSubdistrict ? 'subdistrict' : 'district';
            $destinationName = $useSubdistrict
                ? ($this->subdistrictName ?: 'Unknown Subdistrict')
                : ($this->districts[$this->districtId] ?? 'Unknown District');

            // Enhanced logging to show exactly what's being calculated
            \Log::info('Shipping Cost Calculation Request', [
                'calculation_level' => strtoupper($locationType),
                'origin' => [
                    'branch' => $this->selectedBranch->name,
                    'province' => ($this->selectedBranch->province_name ?? ''),
                    'city' => ($this->selectedBranch->city_name ?? ''),
                    'subdistrict' => ($this->selectedBranch->subdistrict_name ?? ''),
                    'subdistrict_id' => $originId,
                ],
                'destination' => [
                    'province' => $this->provinces[$this->provinceId] ?? 'Unknown',
                    'city' => $this->cities[$this->cityId] ?? 'Unknown',
                    'district' => $this->districts[$this->districtId] ?? 'Unknown',
                    'subdistrict' => $useSubdistrict ? $destinationName : 'N/A',
                    'id' => $destinationId,
                    'type' => $locationType,
                ],
                'weight_grams' => $totalWeight,
            ]);

            // Prepare parameters for shipping cost calculation
            $params = [
                'origin' => $originId,
                'destination' => $destinationId,
                'weight' => max(200, $totalWeight), // Weight in grams, minimum 200
                'courier' => 'ide:tiki:sap:jne:sicepat:jnt:ninja:pos:wahana',
                'price' => 'lowest', // Get lowest price first
                '_use_subdistrict' => $useSubdistrict, // Internal flag for service logging
            ];

            // Calculate shipping cost using the domestic cost endpoint
            // This endpoint supports BOTH district and subdistrict IDs
            $shippingResults = $rajaongkir->calculateDomesticCost($params);

            // Log API response for debugging
            \Log::info('Rajaongkir API Response', [
                'calculation_level' => strtoupper($locationType),
                'results_count' => count($shippingResults),
                'results' => $shippingResults,
            ]);

            // Process results and update shipping options
            $this->processShippingResults($shippingResults);

        } catch (\Exception $e) {
            // Log error but don't break the flow
            \Log::error('Shipping cost calculation error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            // Clear shipping methods on error
            $this->shippingMethods = [];
        }
    }

    /**
     * Process shipping results from Rajaongkir API
     */
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
                'name' => "{$courierName} - {$service}",
                'description' => $description,
                'cost' => $cost,
                'estimatedDays' => $etd ?? '',
            ];
        }

        // Update shipping methods if we have options
        if (! empty($shippingOptions)) {
            // Ensure all data is serializable by converting to plain arrays
            $this->shippingMethods = array_map(function ($option) {
                return [
                    'id' => (string) $option['id'],
                    'name' => (string) $option['name'],
                    'description' => (string) $option['description'],
                    'cost' => (int) $option['cost'],
                    'estimatedDays' => (string) $option['estimatedDays'],
                ];
            }, $shippingOptions);
            // Set first option as default
            $this->shippingMethod = $this->shippingMethods[0]['id'] ?? 'regular';
        } else {
            // No shipping options available from API
            $this->shippingMethods = [];
        }

        // Update shipping cost based on selected method
        $this->updateShippingCost();
    }

    /**
     * Initialize default shipping methods - DEPRECATED
     * Shipping methods are now only shown after district selection
     */
    protected function initializeDefaultShippingMethods()
    {
        // This method is kept for backward compatibility but should not be called
        // Shipping methods are now populated from Rajaongkir API only
    }

    /**
     * Select the nearest branch for shipping origin
     */
    public function selectNearestBranch()
    {
        // This method is no longer needed as we use computed property
    }

    public function placeOrder()
    {
        // Validate all fields
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('validation-failed');
            throw $e;
        }

        // Check if cart is empty
        if ($this->cartItems->isEmpty()) {
            $this->dispatch('showModalError', [
                'title' => 'Keranjang Kosong',
                'message' => 'Keranjang belanja Anda kosong. Silakan tambahkan produk terlebih dahulu.',
            ]);

            return;
        }

        try {
            // Get location names directly from the dropdown arrays (already loaded)
            // This is more efficient and ensures consistency with what user selected
            $provinceName = strtoupper($this->provinces[$this->provinceId] ?? '');
            $cityName = strtoupper($this->cities[$this->cityId] ?? '');
            $districtName = strtoupper($this->districts[$this->districtId] ?? '');
            $subdistrictName = strtoupper($this->subdistricts[$this->subdistrictId] ?? '');

            // Create order using OrderService
            $orderService = new OrderService;

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
                'payment_method' => $this->paymentMethod,
                'service_fee' => $this->serviceFee,
                'discount' => $this->discount,
            ]);

            // Log order creation
            \Log::info('Order created successfully: '.$order->order_number);

            // Create Midtrans payment for non-COD payments
            if ($this->paymentMethod !== 'cod') {
                $midtransService = new MidtransService;
                $paymentResult = $midtransService->createTransaction($order, $this->paymentMethod);

                // Log the payment result for debugging
                \Log::info('Midtrans payment creation result: '.json_encode($paymentResult));

                if ($paymentResult['success']) {
                    // Payment created successfully, the payment record is automatically created by the MidtransService
                    \Log::info('Payment record created successfully for order: '.$order->order_number);
                } else {
                    // Log the error
                    \Log::error('Failed to create payment for order: '.$order->order_number.' - '.$paymentResult['message']);
                    $this->dispatch('showModalError', [
                        'title' => 'Gagal Membuat Pembayaran',
                        'message' => 'Gagal membuat pembayaran: '.$paymentResult['message'],
                    ]);

                    return;
                }
            } else {
                // For COD orders, mark as pending payment
                $order->update([
                    'status' => 'pending_payment',
                    'payment_status' => 'pending',
                ]);
            }

            // Log redirect information
            \Log::info('Redirecting to payment page for order: '.$order->order_number);

            // Show success modal and redirect
            $this->dispatch('showModalSuccess', [
                'title' => 'Pesanan Berhasil Dibuat',
                'message' => 'Pesanan Anda telah berhasil dibuat. Nomor pesanan: '.$order->order_number,
            ]);

            // Redirect after a short delay
            return redirect()->route('payment', ['code' => $order->order_number]);

        } catch (\Exception $e) {
            \Log::error('Order placement error: '.$e->getMessage());
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
}
