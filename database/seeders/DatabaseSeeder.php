<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // ========== CORE CONFIGURATION ==========
            // No dependencies - must run first for system configs
            GlobalConfigSeeder::class,
            PageSeeder::class,
            TrackingCodeSeeder::class,

            // ========== AUTHENTICATION & ROLES ==========
            // FilamentPermissionSeeder is called inside RoleSeeder, but can be explicit
            FilamentPermissionSeeder::class,
            RoleSeeder::class,
            FilamentPermissionSeeder::class,
            UserSeeder::class,

            // ========== PRODUCT MANAGEMENT ==========
            // Must execute in dependency order: Categories → Products → SKUs
            BranchSeeder::class,                        // Warehouse branches
            CategorySeeder::class,                      // Product categories
            ProductSeeder::class,                       // Requires: CategorySeeder
            SkuSeeder::class,                           // Requires: ProductSeeder
            BranchInventorySeeder::class,               // Requires: BranchSeeder, SkuSeeder
                                                        // ⚠️ CLEARS existing branch_inventory data

            // ========== ORDERS (TEST DATA) ==========
            TestOrdersSeeder::class,                    // Requires: BranchSeeder, SkuSeeder, UserSeeder
                                                        // ⚠️ CLEARS orders & order_items tables

            // ========== OPTIONAL: DEMO DATA ONLY ==========
            // These seeders create demo/test data and can be skipped if not needed
            // Comment out the lines below if you want to skip demo data seeding
            // Note: Some demo seeders require prior seeders to have run

            MemberWalletDemoSeeder::class,              // OPTIONAL: Requires: UserSeeder
                                                        // Creates demo wallet entries for users

            OrderSeeder::class,                         // OPTIONAL: Requires: ProductSeeder, UserSeeder
                                                        // Creates 100 sample orders

            ProductReviewSeeder::class,                 // OPTIONAL: Requires: Products must exist
                                                        // Creates sample product reviews

            TestimonialSeeder::class,                   // OPTIONAL: No dependencies
                                                        // Landing page testimonials

            UserRBACSeeder::class,                      // OPTIONAL: Creates its own demo RBAC users
                                                        // (admin@buatserba.com, finance@..., warehouse@...)

            InternalSaleSeeder::class,                  // OPTIONAL: Requires: UserSeeder
                                                        // Creates internal sales records with demo data
        ]);
    }
}
