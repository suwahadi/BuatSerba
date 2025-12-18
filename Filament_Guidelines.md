```
# Laravel 12 + Filament v4 – Resource Guidelines

Standar ini digunakan untuk semua Filament Resource di project Laravel 12.

---

## 1. Redirect Rule (WAJIB)

**Setiap form Create/Edit HARUS redirect ke halaman index resource setelah sukses disimpan.**

Contoh pada halaman `CreateRecord` dan `EditRecord`:

```

protected function getRedirectUrl(): string
{
return \$this->getResource()::getUrl('index');
}

```

Contoh:  
- Dari `/admin/users/create` → setelah save kembali ke `/admin/users`  
- Dari `/admin/users/1/edit` → setelah save kembali ke `/admin/users`

---

## 2. Simple Mode Resources

Untuk resource dengan field sedikit (± < 8 field), gunakan mode simple (tanpa panel/form kompleks).

**Contoh tipe resource simple:**

- Category
- Promo / Discount
- Tag
- Status
- Brand
- Size
- Color

**Contoh perintah artisan (ilustrasi):**

```

php artisan make:filament-resource Category --simple
php artisan make:filament-resource Promo --simple

```

Tujuan: form cepat, ringan, dan tidak berlebihan untuk tabel yang sederhana.

---

## 3. Nonaktifkan Mass / Bulk Delete

**Tidak boleh ada mass delete action atau bulk action lain di tabel.**

Di method `table()` pada Resource:

```

public static function table(Table \$table): Table
{
return \$table
->columns([
// ...
])
->filters([
// ...
])
->actions([
Tables\Actions\EditAction::make(),
// Tambahkan tindakan lain jika perlu, kecuali mass delete
])
->bulkActions([
// Kosong – tidak ada bulk actions
]);
}

```

Atau di List Page (jika override):

```

public function getTableBulkActions(): array
{
return [];
}

```

---

## 4. Ikon Navigasi yang Konsisten

Gunakan ikon yang relevan dengan konteks resource. Sesuaikan dengan icon set yang dipakai (misal: Heroicons / Lucide).

**Rekomendasi mapping ikon:**

- User / Admin: `heroicon-o-user-group`
- Customer: `heroicon-o-user-check`
- Category: `heroicon-o-rectangle-stack`
- Product: `heroicon-o-shopping-bag`
- Order: `heroicon-o-shopping-cart`
- Invoice / Billing: `heroicon-o-document-text`
- Promo / Discount: `heroicon-o-percent-badge`
- Brand: `heroicon-o-tag`
- Article / Blog / News: `heroicon-o-newspaper`
- Gallery / Media: `heroicon-o-photo`
- Contact / Message: `heroicon-o-chat-bubble-left-right`
- Testimonial / Review: `heroicon-o-quote`
- Settings / Configuration: `heroicon-o-cog-6-tooth`
- Dashboard: `heroicon-o-squares-2x2`
- Report / Analytics: `heroicon-o-chart-bar`

Contoh di Resource:

```

protected static ?string \$navigationIcon = 'heroicon-o-shopping-bag';

```

---

## 5. Struktur Resource Standar

Template umum untuk setiap Resource:

```

class ProductResource extends Resource
{
protected static ?string \$model = Product::class;
protected static ?string \$navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Fields...
            ])
            ->columns(2);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Columns...
            ])
            ->filters([
                // Filters...
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]) // Tidak ada bulk action
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            // Relations...
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
    }

```

---

## 6. Kustomisasi Create & Edit Page

Selalu pastikan Create dan Edit menggunakan redirect yang konsisten:

```

class CreateProduct extends CreateRecord
{
protected static string \$resource = ProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    }

class EditProduct extends EditRecord
{
protected static string \$resource = ProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    }

```

---

## 7. Best Practices Tambahan

- **Table**
  - Kolom penting dibuat `searchable()` dan `sortable()`.
  - Gunakan `dateTime()` untuk kolom tanggal.
- **Form**
  - Gunakan `->required()` secara konsisten di field wajib.
  - Untuk relasi many-to-many, pertimbangkan `Select` dengan `->multiple()` atau `RelationManager`.
- **Filters**
  - Tambahkan filter Status (Active/Inactive), Date Range, dan kategori jika relevan.
- **Warna Action**
  - success: `->color('success')`
  - danger: `->color('danger')`
  - warning: `->color('warning')`

---

Gunakan guideline ini sebagai standar saat membuat atau mengedit Filament Resource di proyek Laravel 12 agar UX admin konsisten, aman, dan mudah dipelihara.
```

