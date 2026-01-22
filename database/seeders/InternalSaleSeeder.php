<?php

namespace Database\Seeders;

use App\Models\InternalSale;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class InternalSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Role::count() === 0) {
            $this->call(RoleSeeder::class);
        }

        $userIds = User::role(['admin', 'warehouse'])->pluck('id')->toArray();

        if (empty($userIds)) {
            $user = User::factory()->create();
            $user->assignRole('admin');
            $userIds = [$user->id];
        }

        $faker = \Faker\Factory::create('id_ID');

        $products = [
    'Benang Katun White', 'Benang Katun Black', 'Benang Katun Red', 'Benang Katun Blue', 'Benang Katun Green', 'Benang Katun Yellow', 'Benang Katun Pink', 'Benang Katun Purple', 'Benang Katun Orange', 'Benang Katun Grey',
    'Benang Katun Navy', 'Benang Katun Brown', 'Benang Katun Beige', 'Benang Katun Cream', 'Benang Katun Turquoise', 'Benang Katun Lavender', 'Benang Katun Maroon', 'Benang Katun Teal', 'Benang Katun Olive', 'Benang Katun Mustard',
    'Benang Polyester White', 'Benang Polyester Black', 'Benang Polyester Red', 'Benang Polyester Blue', 'Benang Polyester Green', 'Benang Polyester Yellow', 'Benang Polyester Pink', 'Benang Polyester Purple', 'Benang Polyester Orange', 'Benang Polyester Grey',
    'Benang Polyester Navy', 'Benang Polyester Brown', 'Benang Polyester Beige', 'Benang Polyester Cream', 'Benang Polyester Turquoise', 'Benang Polyester Lavender', 'Benang Polyester Maroon', 'Benang Polyester Teal', 'Benang Polyester Olive', 'Benang Polyester Mustard',
    'Benang Silk White', 'Benang Silk Black', 'Benang Silk Red', 'Benang Silk Blue', 'Benang Silk Green', 'Benang Silk Yellow', 'Benang Silk Pink', 'Benang Silk Purple', 'Benang Silk Orange', 'Benang Silk Grey',
    'Kain Satin Silk White', 'Kain Satin Silk Black', 'Kain Satin Silk Red', 'Kain Satin Silk Blue', 'Kain Satin Silk Green', 'Kain Satin Silk Yellow', 'Kain Satin Silk Pink', 'Kain Satin Silk Purple', 'Kain Satin Silk Orange', 'Kain Satin Silk Grey',
    'Kain Satin Silk Navy', 'Kain Satin Silk Brown', 'Kain Satin Silk Beige', 'Kain Satin Silk Cream', 'Kain Satin Silk Turquoise', 'Kain Satin Silk Lavender', 'Kain Satin Silk Maroon', 'Kain Satin Silk Teal', 'Kain Satin Silk Olive', 'Kain Satin Silk Mustard',
    'Kain Satin Velvet White', 'Kain Satin Velvet Black', 'Kain Satin Velvet Red', 'Kain Satin Velvet Blue', 'Kain Satin Velvet Green', 'Kain Satin Velvet Yellow', 'Kain Satin Velvet Pink', 'Kain Satin Velvet Purple', 'Kain Satin Velvet Orange', 'Kain Satin Velvet Grey',
    'Kain Satin Velvet Navy', 'Kain Satin Velvet Brown', 'Kain Satin Velvet Beige', 'Kain Satin Velvet Cream', 'Kain Satin Velvet Turquoise', 'Kain Satin Velvet Lavender', 'Kain Satin Velvet Maroon', 'Kain Satin Velvet Teal', 'Kain Satin Velvet Olive', 'Kain Satin Velvet Mustard',
    'Kain Chiffon White', 'Kain Chiffon Black', 'Kain Chiffon Red', 'Kain Chiffon Blue', 'Kain Chiffon Green', 'Kain Chiffon Yellow', 'Kain Chiffon Pink', 'Kain Chiffon Purple', 'Kain Chiffon Orange', 'Kain Chiffon Grey',
    'Kain Chiffon Navy', 'Kain Chiffon Brown', 'Kain Chiffon Beige', 'Kain Chiffon Cream', 'Kain Chiffon Turquoise', 'Kain Chiffon Lavender', 'Kain Chiffon Maroon', 'Kain Chiffon Teal', 'Kain Chiffon Olive', 'Kain Chiffon Mustard',
    'Kain Denim White', 'Kain Denim Black', 'Kain Denim Red', 'Kain Denim Blue', 'Kain Denim Green', 'Kain Denim Yellow', 'Kain Denim Pink', 'Kain Denim Purple', 'Kain Denim Orange', 'Kain Denim Grey',
    'Kain Denim Navy', 'Kain Denim Brown', 'Kain Denim Beige', 'Kain Denim Cream', 'Kain Denim Turquoise', 'Kain Denim Lavender', 'Kain Denim Maroon', 'Kain Denim Teal', 'Kain Denim Olive', 'Kain Denim Mustard',
    'Resleting Jepang 25cm', 'Resleting Jepang 50cm', 'Resleting Jepang 75cm', 'Resleting Jepang 100cm', 'Resleting Jepang 125cm', 'Resleting Jepang 150cm', 'Resleting Jepang 175cm', 'Resleting Jepang 200cm', 'Resleting Jepang 225cm', 'Resleting Jepang 250cm',
    'Resleting YKK 25cm', 'Resleting YKK 50cm', 'Resleting YKK 75cm', 'Resleting YKK 100cm', 'Resleting YKK 125cm', 'Resleting YKK 150cm', 'Resleting YKK 175cm', 'Resleting YKK 200cm', 'Resleting YKK 225cm', 'Resleting YKK 250cm',
    'Resleting Coil 25cm', 'Resleting Coil 50cm', 'Resleting Coil 75cm', 'Resleting Coil 100cm', 'Resleting Coil 125cm', 'Resleting Coil 150cm', 'Resleting Coil 175cm', 'Resleting Coil 200cm', 'Resleting Coil 225cm', 'Resleting Coil 250cm',
    'Kancing Batok Kelapa 10mm', 'Kancing Batok Kelapa 12mm', 'Kancing Batok Kelapa 14mm', 'Kancing Batok Kelapa 16mm', 'Kancing Batok Kelapa 18mm', 'Kancing Batok Kelapa 20mm', 'Kancing Batok Kelapa 22mm', 'Kancing Batok Kelapa 24mm', 'Kancing Batok Kelapa 26mm', 'Kancing Batok Kelapa 28mm',
    'Kancing Kemeja Putih 10mm', 'Kancing Kemeja Putih 12mm', 'Kancing Kemeja Putih 14mm', 'Kancing Kemeja Putih 16mm', 'Kancing Kemeja Putih 18mm', 'Kancing Kemeja Putih 20mm', 'Kancing Kemeja Putih 22mm', 'Kancing Kemeja Putih 24mm', 'Kancing Kemeja Putih 26mm', 'Kancing Kemeja Putih 28mm',
    'Kancing Kemeja Hitam 10mm', 'Kancing Kemeja Hitam 12mm', 'Kancing Kemeja Hitam 14mm', 'Kancing Kemeja Hitam 16mm', 'Kancing Kemeja Hitam 18mm', 'Kancing Kemeja Hitam 20mm', 'Kancing Kemeja Hitam 22mm', 'Kancing Kemeja Hitam 24mm', 'Kancing Kemeja Hitam 26mm', 'Kancing Kemeja Hitam 28mm',
    'Kancing Plastik White 10mm', 'Kancing Plastik White 12mm', 'Kancing Plastik White 14mm', 'Kancing Plastik White 16mm', 'Kancing Plastik White 18mm', 'Kancing Plastik White 20mm', 'Kancing Plastik White 22mm', 'Kancing Plastik White 24mm', 'Kancing Plastik White 26mm', 'Kancing Plastik White 28mm',
    'Kancing Plastik Black 10mm', 'Kancing Plastik Black 12mm', 'Kancing Plastik Black 14mm', 'Kancing Plastik Black 16mm', 'Kancing Plastik Black 18mm', 'Kancing Plastik Black 20mm', 'Kancing Plastik Black 22mm', 'Kancing Plastik Black 24mm', 'Kancing Plastik Black 26mm', 'Kancing Plastik Black 28mm',
    'Jarum Jahit Organ Size 70', 'Jarum Jahit Organ Size 75', 'Jarum Jahit Organ Size 80', 'Jarum Jahit Organ Size 85', 'Jarum Jahit Organ Size 90', 'Jarum Jahit Organ Size 95', 'Jarum Jahit Organ Size 100', 'Jarum Jahit Organ Size 105', 'Jarum Jahit Organ Size 110', 'Jarum Jahit Organ Size 115',
    'Jarum Jahit Singer Size 70', 'Jarum Jahit Singer Size 75', 'Jarum Jahit Singer Size 80', 'Jarum Jahit Singer Size 85', 'Jarum Jahit Singer Size 90', 'Jarum Jahit Singer Size 95', 'Jarum Jahit Singer Size 100', 'Jarum Jahit Singer Size 105', 'Jarum Jahit Singer Size 110', 'Jarum Jahit Singer Size 115',
    'Jarum Jahit Brother Size 70', 'Jarum Jahit Brother Size 75', 'Jarum Jahit Brother Size 80', 'Jarum Jahit Brother Size 85', 'Jarum Jahit Brother Size 90', 'Jarum Jahit Brother Size 95', 'Jarum Jahit Brother Size 100', 'Jarum Jahit Brother Size 105', 'Jarum Jahit Brother Size 110', 'Jarum Jahit Brother Size 115',
    'Pita Satin 1 inch White', 'Pita Satin 1 inch Black', 'Pita Satin 1 inch Red', 'Pita Satin 1 inch Blue', 'Pita Satin 1 inch Green', 'Pita Satin 1 inch Yellow', 'Pita Satin 1 inch Pink', 'Pita Satin 1 inch Purple', 'Pita Satin 1 inch Orange', 'Pita Satin 1 inch Grey',
    'Pita Satin 2 inch White', 'Pita Satin 2 inch Black', 'Pita Satin 2 inch Red', 'Pita Satin 2 inch Blue', 'Pita Satin 2 inch Green', 'Pita Satin 2 inch Yellow', 'Pita Satin 2 inch Pink', 'Pita Satin 2 inch Purple', 'Pita Satin 2 inch Orange', 'Pita Satin 2 inch Grey',
    'Pita Grosgrain 1 inch White', 'Pita Grosgrain 1 inch Black', 'Pita Grosgrain 1 inch Red', 'Pita Grosgrain 1 inch Blue', 'Pita Grosgrain 1 inch Green', 'Pita Grosgrain 1 inch Yellow', 'Pita Grosgrain 1 inch Pink', 'Pita Grosgrain 1 inch Purple', 'Pita Grosgrain 1 inch Orange', 'Pita Grosgrain 1 inch Grey',
    'Pita Grosgrain 2 inch White', 'Pita Grosgrain 2 inch Black', 'Pita Grosgrain 2 inch Red', 'Pita Grosgrain 2 inch Blue', 'Pita Grosgrain 2 inch Green', 'Pita Grosgrain 2 inch Yellow', 'Pita Grosgrain 2 inch Pink', 'Pita Grosgrain 2 inch Purple', 'Pita Grosgrain 2 inch Orange', 'Pita Grosgrain 2 inch Grey',
    'Renda Bordir Floral White', 'Renda Bordir Floral Black', 'Renda Bordir Floral Red', 'Renda Bordir Floral Blue', 'Renda Bordir Floral Green', 'Renda Bordir Floral Yellow', 'Renda Bordir Floral Pink', 'Renda Bordir Floral Purple', 'Renda Bordir Floral Orange', 'Renda Bordir Floral Grey',
    'Renda Bordir Geometric White', 'Renda Bordir Geometric Black', 'Renda Bordir Geometric Red', 'Renda Bordir Geometric Blue', 'Renda Bordir Geometric Green', 'Renda Bordir Geometric Yellow', 'Renda Bordir Geometric Pink', 'Renda Bordir Geometric Purple', 'Renda Bordir Geometric Orange', 'Renda Bordir Geometric Grey',
    'Renda Bordir Lace White', 'Renda Bordir Lace Black', 'Renda Bordir Lace Red', 'Renda Bordir Lace Blue', 'Renda Bordir Lace Green', 'Renda Bordir Lace Yellow', 'Renda Bordir Lace Pink', 'Renda Bordir Lace Purple', 'Renda Bordir Lace Orange', 'Renda Bordir Lace Grey',
    'Kain Tile Halus White', 'Kain Tile Halus Black', 'Kain Tile Halus Red', 'Kain Tile Halus Blue', 'Kain Tile Halus Green', 'Kain Tile Halus Yellow', 'Kain Tile Halus Pink', 'Kain Tile Halus Purple', 'Kain Tile Halus Orange', 'Kain Tile Halus Grey',
    'Kain Tile Halus Navy', 'Kain Tile Halus Brown', 'Kain Tile Halus Beige', 'Kain Tile Halus Cream', 'Kain Tile Halus Turquoise', 'Kain Tile Halus Lavender', 'Kain Tile Halus Maroon', 'Kain Tile Halus Teal', 'Kain Tile Halus Olive', 'Kain Tile Halus Mustard',
    'Kain Tile Kasar White', 'Kain Tile Kasar Black', 'Kain Tile Kasar Red', 'Kain Tile Kasar Blue', 'Kain Tile Kasar Green', 'Kain Tile Kasar Yellow', 'Kain Tile Kasar Pink', 'Kain Tile Kasar Purple', 'Kain Tile Kasar Orange', 'Kain Tile Kasar Grey',
    'Kain Tile Kasar Navy', 'Kain Tile Kasar Brown', 'Kain Tile Kasar Beige', 'Kain Tile Kasar Cream', 'Kain Tile Kasar Turquoise', 'Kain Tile Kasar Lavender', 'Kain Tile Kasar Maroon', 'Kain Tile Kasar Teal', 'Kain Tile Kasar Olive', 'Kain Tile Kasar Mustard',
    'Karet Elastis 2cm White', 'Karet Elastis 2cm Black', 'Karet Elastis 2cm Red', 'Karet Elastis 2cm Blue', 'Karet Elastis 2cm Green', 'Karet Elastis 2cm Yellow', 'Karet Elastis 2cm Pink', 'Karet Elastis 2cm Purple', 'Karet Elastis 2cm Orange', 'Karet Elastis 2cm Grey',
    'Karet Elastis 4cm White', 'Karet Elastis 4cm Black', 'Karet Elastis 4cm Red', 'Karet Elastis 4cm Blue', 'Karet Elastis 4cm Green', 'Karet Elastis 4cm Yellow', 'Karet Elastis 4cm Pink', 'Karet Elastis 4cm Purple', 'Karet Elastis 4cm Orange', 'Karet Elastis 4cm Grey',
    'Karet Elastis 6cm White', 'Karet Elastis 6cm Black', 'Karet Elastis 6cm Red', 'Karet Elastis 6cm Blue', 'Karet Elastis 6cm Green', 'Karet Elastis 6cm Yellow', 'Karet Elastis 6cm Pink', 'Karet Elastis 6cm Purple', 'Karet Elastis 6cm Orange', 'Karet Elastis 6cm Grey',
    'Velcro Tape 2.5cm White', 'Velcro Tape 2.5cm Black', 'Velcro Tape 2.5cm Red', 'Velcro Tape 2.5cm Blue', 'Velcro Tape 2.5cm Green', 'Velcro Tape 2.5cm Yellow', 'Velcro Tape 2.5cm Pink', 'Velcro Tape 2.5cm Purple', 'Velcro Tape 2.5cm Orange', 'Velcro Tape 2.5cm Grey',
    'Velcro Tape 5cm White', 'Velcro Tape 5cm Black', 'Velcro Tape 5cm Red', 'Velcro Tape 5cm Blue', 'Velcro Tape 5cm Green', 'Velcro Tape 5cm Yellow', 'Velcro Tape 5cm Pink', 'Velcro Tape 5cm Purple', 'Velcro Tape 5cm Orange', 'Velcro Tape 5cm Grey',
    'Gunting Kain 8 inch', 'Gunting Kain 10 inch', 'Gunting Kain 12 inch', 'Gunting Kain 14 inch', 'Gunting Kain 16 inch', 'Gunting Kain 18 inch', 'Gunting Kain 20 inch', 'Gunting Kain 22 inch', 'Gunting Kain 24 inch', 'Gunting Kain 26 inch',
    'Meteran Jahit 150cm', 'Meteran Jahit 200cm', 'Meteran Jahit 250cm', 'Meteran Jahit 300cm', 'Meteran Jahit 350cm', 'Meteran Jahit 400cm', 'Meteran Jahit 450cm', 'Meteran Jahit 500cm', 'Meteran Jahit 550cm', 'Meteran Jahit 600cm',
    'Kapur Jahit Segitiga White', 'Kapur Jahit Segitiga Black', 'Kapur Jahit Segitiga Red', 'Kapur Jahit Segitiga Blue', 'Kapur Jahit Segitiga Green', 'Kapur Jahit Segitiga Yellow', 'Kapur Jahit Segitiga Pink', 'Kapur Jahit Segitiga Purple', 'Kapur Jahit Segitiga Orange', 'Kapur Jahit Segitiga Grey',
    'Kapur Jahit Pensil White', 'Kapur Jahit Pensil Black', 'Kapur Jahit Pensil Red', 'Kapur Jahit Pensil Blue', 'Kapur Jahit Pensil Green', 'Kapur Jahit Pensil Yellow', 'Kapur Jahit Pensil Pink', 'Kapur Jahit Pensil Purple', 'Kapur Jahit Pensil Orange', 'Kapur Jahit Pensil Grey',
    'Minyak Mesin Jahit 100ml', 'Minyak Mesin Jahit 200ml', 'Minyak Mesin Jahit 300ml', 'Minyak Mesin Jahit 400ml', 'Minyak Mesin Jahit 500ml', 'Minyak Mesin Jahit 600ml', 'Minyak Mesin Jahit 700ml', 'Minyak Mesin Jahit', 'Kaos Polos Black', 'Kaos Polos White', 'Kaos Polos Red', 'Kaos Polos Blue', 'Kaos Polos Green', 'Kaos Polos Yellow', 'Kaos Polos Pink', 'Kaos Polos Purple', 'Kaos Polos Orange', 'Kaos Polos Grey', 'Kaos Polos Navy', 'Kaos Polos Brown', 'Kaos Polos Beige', 'Kaos Polos Cream', 'Kaos Polos Turquoise', 'Kaos Polos Lavender', 'Kaos Polos Maroon', 'Kaos Polos Teal', 'Kaos Polos Olive', 'Kaos Polos Mustard',
        ];

        $records = [];
        $batchSize = 1000;

        for ($i = 0; $i < $batchSize; $i++) {
            $productName = $faker->randomElement($products);
            $qty = $faker->numberBetween(1, 200);
            $price = $faker->numberBetween(500, 150000);
            $total = $qty * $price;
            
            $bucket = $faker->randomElement([
                'today', 'week', 'last_week', 'month', 'last_month', 'year', 'last_year', 'random'
            ]);

            $transactionDate = match ($bucket) {
                'today' => Carbon::today()->addHours(rand(8, 17))->addMinutes(rand(0, 59)),
                'week' => Carbon::now()->startOfWeek()->addDays(rand(0, 6))->addHours(rand(8, 17)),
                'last_week' => Carbon::now()->subWeek()->startOfWeek()->addDays(rand(0, 6))->addHours(rand(8, 17)),
                'month' => Carbon::now()->startOfMonth()->addDays(rand(0, Carbon::now()->daysInMonth - 1))->addHours(rand(8, 17)),
                'last_month' => Carbon::now()->subMonth()->startOfMonth()->addDays(rand(0, Carbon::now()->subMonth()->daysInMonth - 1))->addHours(rand(8, 17)),
                'year' => Carbon::now()->startOfYear()->addDays(rand(0, 360))->addHours(rand(8, 17)),
                'last_year' => Carbon::now()->subYear()->startOfYear()->addDays(rand(0, 360))->addHours(rand(8, 17)),
                'random' => Carbon::now()->subDays(rand(0, 730))->addHours(rand(8, 17)),
            };

            if ($transactionDate->isFuture()) {
                $transactionDate = Carbon::now();
            }

            $records[] = [
                'user_id' => $faker->randomElement($userIds),
                'code' => 'PT-' . strtoupper($faker->bothify('??#?#?')),
                'name' => $productName,
                'price' => $price,
                'qty' => $qty,
                'total' => $total,
                'transaction_date' => $transactionDate,
                'created_at' => $transactionDate,
                'updated_at' => $transactionDate->copy()->addMinutes(rand(1, 60)),
            ];
        }

        foreach (array_chunk($records, 100) as $chunk) {
            InternalSale::insert($chunk);
        }
    }
}
