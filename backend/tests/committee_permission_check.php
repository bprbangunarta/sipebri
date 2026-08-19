<?php
// QA helper: create/delete a temporary Guest user for 403 permission checks.
// Usage: php artisan tinker --execute="require '/app/backend/tests/committee_permission_check.php';"
$mode = getenv('QA_MODE') ?: 'create';
$email = 'qa_guest_committee@example.test';

if ($mode === 'delete') {
    App\Models\User::where('email', $email)->forceDelete();
    echo "deleted\n";

    return;
}

$user = App\Models\User::withTrashed()->firstOrNew(['email' => $email]);
$user->name = 'QA Guest Committee';
$user->username = 'qaguestcommittee';
$user->password = bcrypt('QaGuest@123');
$user->deleted_at = null;
if (property_exists($user, 'is_active') || array_key_exists('is_active', $user->getAttributes()) || in_array('is_active', $user->getFillable(), true)) {
    $user->is_active = true;
}
$user->save();
$user->syncRoles(['Guest']);
echo "created id={$user->id} roles=".$user->roles->pluck('name')->join(',')." perms=".$user->getAllPermissions()->count()."\n";
