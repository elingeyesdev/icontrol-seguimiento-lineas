<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

foreach (User::with('roles', 'permissions')->get() as $u) {
    echo $u->id.' '.$u->email.' Roles:'.implode(',', $u->getRoleNames()->toArray()).' Perms:'.implode(',', $u->getPermissionNames()->toArray()).PHP_EOL;
}
echo "ROLES\n";
foreach (Role::with('permissions')->get() as $r) {
    echo $r->name.' Perms:'.implode(',', $r->permissions->pluck('name')->toArray()).PHP_EOL;
}
echo "PERMISSIONS TABLE\n";
foreach (Permission::all() as $p) {
    echo $p->id.' '.$p->name.PHP_EOL;
}
echo 'PERMISSIONS COUNT '.Permission::count().PHP_EOL;
