<?php

namespace App\Filament\Resources\ProductReviews\Pages;

use App\Filament\Resources\ProductReviews\ProductReviewResource;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageProductReviews extends ManageRecords
{
    protected static string $resource = ProductReviewResource::class;

    protected static ?string $title = 'Detail Review';

    protected function getTableQuery(): ?Builder
    {
        return parent::getTableQuery()?->with(['product', 'user', 'order']);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
