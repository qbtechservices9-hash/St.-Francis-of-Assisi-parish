<?php
// Temporary script to list user accounts with error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Debug output
echo "Fetching users...\n";


$users = \App\Models\User::with('roles')->get();

echo "USER ACCOUNTS IN DATABASE:\n";
echo str_repeat('-', 75) . "\n";
printf("%-5s | %-25s | %-30s | %-15s\n", "ID", "Name", "Email", "Role(s)");
echo str_repeat('-', 75) . "\n";

foreach ($users as $u) {
    $roles = $u->roles->pluck('name')->join(', ');
    if (empty($roles)) {
        $roles = 'No role assigned';
    }
    printf("%-5d | %-25s | %-30s | %-15s\n", $u->id, substr($u->name, 0, 25), substr($u->email, 0, 30), $roles);
}
echo str_repeat('-', 75) . "\n";
