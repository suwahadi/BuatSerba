<?php

namespace App\Rules;

use App\Models\Sku;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueSkuCode implements ValidationRule
{
    protected $ignoreId;
    protected $isRepeater;

    public function __construct($ignoreId = null, $isRepeater = false)
    {
        $this->ignoreId = $ignoreId;
        $this->isRepeater = $isRepeater;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value) {
            return;
        }

        $query = Sku::where('sku', $value);

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('Maaf, Kode SKU harus unik, tidak boleh sama.');
        }
    }
}
