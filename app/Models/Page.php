<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_active',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function getFirstParagraphAttribute(): string
    {
        if (empty($this->content)) {
            return '';
        }

        if (preg_match('/<p[^>]*>(.*?)<\/p>/is', $this->content, $matches)) {
            return trim(strip_tags($matches[1]));
        }

        return mb_substr(strip_tags($this->content), 0, 200);
    }

    public static function getAboutDescription(): string
    {
        $page = self::where('slug', 'about')
            ->where('is_active', true)
            ->first();

        return $page?->first_paragraph ?? 'Platform belanja online terpercaya dengan produk berkualitas dan harga terbaik. Kami hadir sebagai solusi belanja one-stop shopping untuk memenuhi semua kebutuhan Anda.';
    }
}