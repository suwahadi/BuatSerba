<?php

namespace App\Livewire;

use App\Models\Testimonial;
use Livewire\Component;

class TestimonialCarousel extends Component
{
    public function render()
    {
        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('sort')
            ->get();

        return view('livewire.testimonial-carousel', [
            'testimonials' => $testimonials,
        ]);
    }
}
