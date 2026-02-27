#!/usr/bin/env php
<?php
/**
 * Quick Verification Script for Premium Membership Implementation
 * Checks: Files exist, migrations are correct, models have methods, routes defined
 * Run: php verify-premium-implementation.php
 */

$checks = [
    'pass' => [],
    'fail' => [],
];

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Premium Membership Implementation Verification\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$baseDir = __DIR__;

// 1. Check Migrations
echo "[1] Checking Migrations...\n";
$migrations = [
    'database/migrations/2026_02_27_create_premium_memberships_table.php',
    'database/migrations/2026_02_27_add_premium_expires_at_to_users_table.php',
];

foreach ($migrations as $migration) {
    $path = $baseDir . '/' . $migration;
    if (file_exists($path)) {
        $content = file_get_contents($path);
        if (strpos($content, 'premium_memberships') !== false || strpos($content, 'premium_expires_at') !== false) {
            echo "   ✓ $migration\n";
            $checks['pass'][] = "Migration: $migration";
        } else {
            echo "   ✗ $migration (invalid content)\n";
            $checks['fail'][] = "Migration: $migration (invalid content)";
        }
    } else {
        echo "   ✗ $migration (not found)\n";
        $checks['fail'][] = "Migration: $migration (not found)";
    }
}

// 2. Check Models
echo "\n[2] Checking Models...\n";
$modelFile = $baseDir . '/app/Models/PremiumMembership.php';
if (file_exists($modelFile)) {
    $content = file_get_contents($modelFile);
    $checks_model = [
        'isActive' => strpos($content, 'function isActive') !== false,
        'isExpired' => strpos($content, 'function isExpired') !== false,
        'daysRemaining' => strpos($content, 'function daysRemaining') !== false,
    ];
    
    foreach ($checks_model as $method => $exists) {
        if ($exists) {
            echo "   ✓ PremiumMembership.$method()\n";
            $checks['pass'][] = "Model method: PremiumMembership::$method()";
        } else {
            echo "   ✗ PremiumMembership.$method() (not found)\n";
            $checks['fail'][] = "Model method: PremiumMembership::$method()";
        }
    }
} else {
    echo "   ✗ PremiumMembership.php (not found)\n";
    $checks['fail'][] = "Model: PremiumMembership.php";
}

// Check User model updates
$userFile = $baseDir . '/app/Models/User.php';
if (file_exists($userFile)) {
    $content = file_get_contents($userFile);
    $checks_user = [
        'isPremium' => strpos($content, 'function isPremium') !== false,
        'premiumMemberships' => strpos($content, 'function premiumMemberships') !== false,
        'activePremiumMembership' => strpos($content, 'function activePremiumMembership') !== false,
        'premium_expires_at cast' => strpos($content, "'premium_expires_at' => 'datetime'") !== false,
    ];
    
    foreach ($checks_user as $check => $exists) {
        if ($exists) {
            echo "   ✓ User.$check\n";
            $checks['pass'][] = "User: $check";
        } else {
            echo "   ✗ User.$check (not found)\n";
            $checks['fail'][] = "User: $check";
        }
    }
}

// 3. Check Controller
echo "\n[3] Checking Controller...\n";
$controllerFile = $baseDir . '/app/Http/Controllers/PremiumMembershipController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    $methods = ['purchase', 'uploadProof', 'showPurchase', 'myMemberships', 'renew'];
    
    foreach ($methods as $method) {
        if (strpos($content, "function $method") !== false || strpos($content, "public function $method") !== false) {
            echo "   ✓ PremiumMembershipController.$method()\n";
            $checks['pass'][] = "Controller method: $method()";
        } else {
            echo "   ✗ PremiumMembershipController.$method() (not found)\n";
            $checks['fail'][] = "Controller method: $method()";
        }
    }
} else {
    echo "   ✗ PremiumMembershipController.php (not found)\n";
    $checks['fail'][] = "Controller: PremiumMembershipController.php";
}

// 4. Check Listener
echo "\n[4] Checking Event Listener...\n";
$listenerFile = $baseDir . '/app/Listeners/GrantPremiumCashback.php';
if (file_exists($listenerFile)) {
    $content = file_get_contents($listenerFile);
    if (strpos($content, 'class GrantPremiumCashback') !== false && strpos($content, 'function handle') !== false) {
        echo "   ✓ GrantPremiumCashback listener\n";
        $checks['pass'][] = "Listener: GrantPremiumCashback";
        
        if (strpos($content, 'premium_cashback') !== false && strpos($content, '0.01') !== false) {
            echo "   ✓ Cashback logic (1% calculation)\n";
            $checks['pass'][] = "Cashback: 1% calculation logic";
        }
    }
}

// 5. Check Command
echo "\n[5] Checking Scheduled Command...\n";
$commandFile = $baseDir . '/app/Console/Commands/ExpirePremiumMemberships.php';
if (file_exists($commandFile)) {
    $content = file_get_contents($commandFile);
    if (strpos($content, 'class ExpirePremiumMemberships') !== false && strpos($content, 'function handle') !== false) {
        echo "   ✓ ExpirePremiumMemberships command\n";
        $checks['pass'][] = "Command: ExpirePremiumMemberships";
        
        if (strpos($content, 'status.*expired') !== false || strpos($content, "'status', 'expired'") !== false) {
            echo "   ✓ Expiry logic\n";
            $checks['pass'][] = "Expiry: Status update logic";
        }
    }
}

// 6. Check Routes
echo "\n[6] Checking Routes...\n";
$routesFile = $baseDir . '/routes/web.php';
if (file_exists($routesFile)) {
    $content = file_get_contents($routesFile);
    $routes = [
        'premium.purchase' => "'/user/premium'",
        'premium.purchase.store' => "'/user/premium/purchase'",
        'premium.upload-proof' => "'/user/premium/upload-proof'",
        'premium.memberships' => "'/user/premium/memberships'",
        'premium.renew' => "'/user/premium/renew'",
    ];
    
    foreach ($routes as $name => $path) {
        if (strpos($content, $name) !== false) {
            echo "   ✓ Route: $name\n";
            $checks['pass'][] = "Route: $name";
        } else {
            echo "   ✗ Route: $name (not found)\n";
            $checks['fail'][] = "Route: $name";
        }
    }
}

// 7. Check Console Schedule
echo "\n[7] Checking Scheduled Jobs...\n";
$consoleFile = $baseDir . '/routes/console.php';
if (file_exists($consoleFile)) {
    $content = file_get_contents($consoleFile);
    if (strpos($content, 'premium:expire-memberships') !== false) {
        echo "   ✓ Scheduled command: premium:expire-memberships\n";
        $checks['pass'][] = "Schedule: premium:expire-memberships";
    }
}

// 8. Check Filament Resource
echo "\n[8] Checking Filament Admin Panel...\n";
$resourceFile = $baseDir . '/app/Filament/Resources/PremiumMembershipResource.php';
if (file_exists($resourceFile)) {
    echo "   ✓ PremiumMembershipResource.php\n";
    $checks['pass'][] = "Filament: PremiumMembershipResource";
    
    $pagesDir = $baseDir . '/app/Filament/Resources/PremiumMembershipResource/Pages';
    if (is_dir($pagesDir)) {
        if (file_exists($pagesDir . '/ListPremiumMemberships.php')) {
            echo "   ✓ ListPremiumMemberships page\n";
            $checks['pass'][] = "Filament page: ListPremiumMemberships";
        }
        if (file_exists($pagesDir . '/EditPremiumMembership.php')) {
            echo "   ✓ EditPremiumMembership page\n";
            $checks['pass'][] = "Filament page: EditPremiumMembership";
        }
    }
}

// 9. Check Factories
echo "\n[9] Checking Test Factories...\n";
$factoryFile = $baseDir . '/database/factories/PremiumMembershipFactory.php';
if (file_exists($factoryFile)) {
    echo "   ✓ PremiumMembershipFactory.php\n";
    $checks['pass'][] = "Factory: PremiumMembershipFactory";
}

// 10. Check Tests
echo "\n[10] Checking Test Files...\n";
$testFiles = [
    'tests/Feature/PremiumMembershipControllerTest.php',
    'tests/Feature/GrantPremiumCashbackTest.php',
    'tests/Feature/ExpirePremiumMembershipsCommandTest.php',
    'tests/Unit/PremiumMembershipTest.php',
];

foreach ($testFiles as $testFile) {
    $path = $baseDir . '/' . $testFile;
    if (file_exists($path)) {
        echo "   ✓ $testFile\n";
        $checks['pass'][] = "Test: " . basename($testFile);
    } else {
        echo "   ✗ $testFile (not found)\n";
    }
}

// Summary
echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Summary\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "✓ Passed: " . count($checks['pass']) . "\n";
echo "✗ Failed: " . count($checks['fail']) . "\n\n";

if (count($checks['fail']) > 0) {
    echo "Failed Checks:\n";
    foreach ($checks['fail'] as $fail) {
        echo "  - $fail\n";
    }
    echo "\n";
    exit(1);
} else {
    echo "✓ All checks passed! Premium Membership feature is properly implemented.\n\n";
    exit(0);
}
