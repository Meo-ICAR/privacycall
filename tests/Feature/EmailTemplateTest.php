<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\EmailTemplate;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmailTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'superadmin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }

    /** @test */
    public function superadmin_can_view_email_templates_index()
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('superadmin');

        $response = $this->actingAs($superadmin)->get(route('email-templates.index'));

        $response->assertStatus(200);
        $response->assertViewIs('email-templates.index');
    }

    /** @test */
    public function admin_can_view_email_templates_index()
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create(['company_id' => $company->id]);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('email-templates.index'));

        $response->assertStatus(200);
        $response->assertViewIs('email-templates.index');
    }

    /** @test */
    public function superadmin_can_create_email_template()
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('superadmin');
        $company = Company::factory()->create();

        $response = $this->actingAs($superadmin)->get(route('email-templates.create'));

        $response->assertStatus(200);
        $response->assertViewIs('email-templates.create');
    }

    /** @test */
    public function admin_cannot_create_email_template()
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create(['company_id' => $company->id]);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('email-templates.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function superadmin_can_store_email_template()
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('superadmin');
        $company = Company::factory()->create();

        $templateData = [
            'company_id' => $company->id,
            'name' => 'Test Template',
            'subject' => 'Test Subject',
            'body' => 'Test body content',
            'category' => 'general',
            'is_active' => true,
        ];

        $response = $this->actingAs($superadmin)->post(route('email-templates.store'), $templateData);

        $response->assertRedirect(route('email-templates.index'));
        $this->assertDatabaseHas('email_templates', [
            'name' => 'Test Template',
            'company_id' => $company->id,
        ]);
    }

    /** @test */
    public function admin_cannot_store_email_template()
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create(['company_id' => $company->id]);
        $admin->assignRole('admin');

        $templateData = [
            'company_id' => $company->id,
            'name' => 'Test Template',
            'subject' => 'Test Subject',
            'body' => 'Test body content',
            'category' => 'general',
            'is_active' => true,
        ];

        $response = $this->actingAs($admin)->post(route('email-templates.store'), $templateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function superadmin_can_edit_email_template()
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('superadmin');
        $company = Company::factory()->create();
        $template = EmailTemplate::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($superadmin)->get(route('email-templates.edit', $template));

        $response->assertStatus(200);
        $response->assertViewIs('email-templates.edit');
    }

    /** @test */
    public function admin_cannot_edit_email_template()
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create(['company_id' => $company->id]);
        $admin->assignRole('admin');
        $template = EmailTemplate::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($admin)->get(route('email-templates.edit', $template));

        $response->assertStatus(403);
    }

    /** @test */
    public function superadmin_can_update_email_template()
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('superadmin');
        $company = Company::factory()->create();
        $template = EmailTemplate::factory()->create(['company_id' => $company->id]);

        $updateData = [
            'company_id' => $company->id,
            'name' => 'Updated Template',
            'subject' => 'Updated Subject',
            'body' => 'Updated body content',
            'category' => 'supplier',
            'is_active' => false,
        ];

        $response = $this->actingAs($superadmin)->put(route('email-templates.update', $template), $updateData);

        $response->assertRedirect(route('email-templates.index'));
        $this->assertDatabaseHas('email_templates', [
            'id' => $template->id,
            'name' => 'Updated Template',
        ]);
    }

    /** @test */
    public function admin_cannot_update_email_template()
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create(['company_id' => $company->id]);
        $admin->assignRole('admin');
        $template = EmailTemplate::factory()->create(['company_id' => $company->id]);

        $updateData = [
            'company_id' => $company->id,
            'name' => 'Updated Template',
            'subject' => 'Updated Subject',
            'body' => 'Updated body content',
            'category' => 'supplier',
            'is_active' => false,
        ];

        $response = $this->actingAs($admin)->put(route('email-templates.update', $template), $updateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function superadmin_can_delete_email_template()
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('superadmin');
        $template = EmailTemplate::factory()->create();

        $response = $this->actingAs($superadmin)->delete(route('email-templates.destroy', $template));

        $response->assertRedirect(route('email-templates.index'));
        $this->assertDatabaseMissing('email_templates', ['id' => $template->id]);
    }

    /** @test */
    public function admin_can_delete_own_company_template()
    {
        $company = Company::factory()->create();
        $admin = User::factory()->create(['company_id' => $company->id]);
        $admin->assignRole('admin');
        $template = EmailTemplate::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($admin)->delete(route('email-templates.destroy', $template));

        $response->assertRedirect(route('email-templates.index'));
        $this->assertDatabaseMissing('email_templates', ['id' => $template->id]);
    }

    /** @test */
    public function admin_cannot_delete_other_company_template()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        $admin = User::factory()->create(['company_id' => $company1->id]);
        $admin->assignRole('admin');
        $template = EmailTemplate::factory()->create(['company_id' => $company2->id]);

        $response = $this->actingAs($admin)->delete(route('email-templates.destroy', $template));

        $response->assertStatus(403);
        $this->assertDatabaseHas('email_templates', ['id' => $template->id]);
    }

    /** @test */
    public function superadmin_sees_all_templates()
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('superadmin');
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $template1 = EmailTemplate::factory()->create(['company_id' => $company1->id]);
        $template2 = EmailTemplate::factory()->create(['company_id' => $company2->id]);
        $globalTemplate = EmailTemplate::factory()->create(['company_id' => null]);

        $response = $this->actingAs($superadmin)->get(route('email-templates.index'));

        $response->assertStatus(200);
        $response->assertSee($template1->name);
        $response->assertSee($template2->name);
        $response->assertSee($globalTemplate->name);
    }

    /** @test */
    public function admin_sees_only_own_company_and_global_templates()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        $admin = User::factory()->create(['company_id' => $company1->id]);
        $admin->assignRole('admin');

        $ownTemplate = EmailTemplate::factory()->create(['company_id' => $company1->id]);
        $otherTemplate = EmailTemplate::factory()->create(['company_id' => $company2->id]);
        $globalTemplate = EmailTemplate::factory()->create(['company_id' => null]);

        $response = $this->actingAs($admin)->get(route('email-templates.index'));

        $response->assertStatus(200);
        $response->assertSee($ownTemplate->name);
        $response->assertSee($globalTemplate->name);
        $response->assertDontSee($otherTemplate->name);
    }
}
