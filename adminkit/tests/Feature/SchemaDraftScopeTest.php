<?php

namespace Tests\Feature;

use App\Models\SchemaDraft;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/** Kolom rancangan tidak boleh disunting lewat id rancangan lain. */
class SchemaDraftScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_column_of_other_draft_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(
            Permission::findOrCreate('schema-drafts.view', 'web'),
            Permission::findOrCreate('schema-drafts.manage', 'web'),
        );

        $a = SchemaDraft::create(['name' => 'A', 'table_name' => 'zz_scope_a']);
        $b = SchemaDraft::create(['name' => 'B', 'table_name' => 'zz_scope_b']);
        $column = $a->columns()->create(['sort' => 0, 'name' => 'foo', 'type' => 'string']);

        $this->actingAs($user)
            ->delete("/schema-drafts/{$b->id}/columns/{$column->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('schema_draft_columns', ['id' => $column->id]);

        $this->actingAs($user)
            ->delete("/schema-drafts/{$a->id}/columns/{$column->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('schema_draft_columns', ['id' => $column->id]);
    }
}
