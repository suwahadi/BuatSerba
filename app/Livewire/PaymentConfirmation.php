<?php

namespace App\Livewire;

use App\Jobs\SendPaymentConfirmationEmail;
use App\Models\PaymentConfirmation as PaymentConfirmationModel;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithFileUploads;

class PaymentConfirmation extends Component
{
    use WithFileUploads;

    public $order;
    public $orderNumber;
    
    // Form Fields
    public $sender_name;
    public $sender_bank;
    public $sender_account_number;
    public $proof_file;
    public $notes;

    public $banks = [
        'BCA', 'MANDIRI', 'BNI', 'BRI', 'BSI', 'BTN', 'NISP', 
        'PERMATA', 'DANAMON', 'SEABANK', 'CIMB', 'JAGO', 
        'JENIUS', 'GOPAY', 'LINKAJA', 'DANA', 'OVO', 'SHOPEEPAY'
    ];

    public function mount($code)
    {
        $this->orderNumber = $code;
        $this->order = Order::where('order_number', $code)->firstOrFail();
        
        // Auto-fill sender name if empty
        $this->sender_name = $this->order->customer_name;
    }

    protected $rules = [
        'sender_name' => 'required|string|min:3',
        'sender_bank' => 'required|string',
        'sender_account_number' => 'required|string|min:4',
        'proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
        'notes' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'sender_name.required' => 'Nama pengirim harus diisi.',
        'sender_bank.required' => 'Bank pengirim harus dipilih.',
        'sender_bank.in' => 'Bank pengirim tidak valid.',
        'sender_account_number.required' => 'Nomor rekening pengirim harus diisi.',
        'proof_file.required' => 'Bukti transfer wajib diupload.',
        'proof_file.mimes' => 'Format file harus berupa JPG, JPEG, PNG, atau PDF.',
        'proof_file.max' => 'Ukuran file maksimal 2MB.',
    ];

    public function submit()
    {
        $this->validate();

        // Ensure directory exists
        // Save file properly using storage disk
        $filename = $this->proof_file->store('payment-proofs', 'public');
        
        // Full filesystem path for attachment
        $fullPath = storage_path('app/public/' . $filename);
        
        // Public URL for email link
        $proofUrl = asset('storage/' . $filename);

        $data = [
            'sender_name' => $this->sender_name,
            'sender_bank' => $this->sender_bank,
            'sender_account_number' => $this->sender_account_number,
            'notes' => $this->notes,
            'proof_url' => $proofUrl,
        ];

        $existing = PaymentConfirmationModel::query()
            ->where('order_id', $this->order->id)
            ->first();

        if ($existing?->bukti_transfer_path) {
            $oldFullPath = storage_path('app/public/' . $existing->bukti_transfer_path);
            if (is_file($oldFullPath)) {
                @unlink($oldFullPath);
            }
        }

        PaymentConfirmationModel::updateOrCreate(
            ['order_id' => $this->order->id],
            [
                'nama_lengkap' => $this->sender_name,
                'bank' => $this->sender_bank,
                'nomor_rekening' => $this->sender_account_number,
                'bukti_transfer_path' => $filename,
                'catatan' => $this->notes,
                'confirmed_at' => now(),
                'is_read' => false,
                'read_at' => null,
            ]
        );

        // Dispatch Job
        SendPaymentConfirmationEmail::dispatch($this->order, $data, $fullPath);

        // Flash success message
        session()->flash('success', 'Konfirmasi pembayaran berhasil dikirim. Kami akan memvalidasi pembayaran Anda secepatnya.');

        // Redirect back to payment page
        return redirect()->route('payment', ['code' => $this->orderNumber]);
    }

    public function render()
    {
        return view('livewire.payment-confirmation')->layout('components.layouts.guest');
    }
}
