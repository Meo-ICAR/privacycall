<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Representative;
use App\Models\Company;

class RepresentativeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test company
        $this->company = Company::factory()->create();
    }

    /** @test */
    public function it_can_list_representatives()
    {
        Representative::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->getJson('/api/v1/representatives');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'company_id',
                                'first_name',
                                'last_name',
                                'email',
                                'is_active',
                                'company'
                            ]
                        ]
                    ],
                    'message'
                ]);
    }

    /** @test */
    public function it_can_create_a_representative()
    {
        $data = [
            'company_id' => $this->company->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1234567890',
            'position' => 'Data Protection Officer',
            'department' => 'Legal',
            'disclosure_subscriptions' => ['gdpr_updates', 'data_breach_notifications'],
            'is_active' => true,
            'email_notifications' => true,
            'sms_notifications' => false,
            'preferred_contact_method' => 'email',
            'notes' => 'Test representative'
        ];

        $response = $this->postJson('/api/v1/representatives', $data);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Representative created successfully'
                ]);

        $this->assertDatabaseHas('representatives', [
            'email' => 'john.doe@example.com',
            'company_id' => $this->company->id
        ]);
    }

    /** @test */
    public function it_can_show_a_representative()
    {
        $representative = Representative::factory()->create(['company_id' => $this->company->id]);

        $response = $this->getJson("/api/v1/representatives/{$representative->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $representative->id,
                        'email' => $representative->email
                    ]
                ])
                ->assertJsonStructure([
                    'disclosure_summary'
                ]);
    }

    /** @test */
    public function it_can_update_a_representative()
    {
        $representative = Representative::factory()->create(['company_id' => $this->company->id]);

        $updateData = [
            'first_name' => 'Updated Name',
            'position' => 'Updated Position',
            'is_active' => false
        ];

        $response = $this->putJson("/api/v1/representatives/{$representative->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Representative updated successfully'
                ]);

        $this->assertDatabaseHas('representatives', [
            'id' => $representative->id,
            'first_name' => 'Updated Name',
            'position' => 'Updated Position',
            'is_active' => false
        ]);
    }

    /** @test */
    public function it_can_delete_a_representative()
    {
        $representative = Representative::factory()->create(['company_id' => $this->company->id]);

        $response = $this->deleteJson("/api/v1/representatives/{$representative->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Representative deleted successfully'
                ]);

        $this->assertSoftDeleted('representatives', ['id' => $representative->id]);
    }

    /** @test */
    public function it_can_add_disclosure_subscription()
    {
        $representative = Representative::factory()->create([
            'company_id' => $this->company->id,
            'disclosure_subscriptions' => ['gdpr_updates']
        ]);

        $response = $this->postJson("/api/v1/representatives/{$representative->id}/add-disclosure-subscription", [
            'disclosure_type' => 'data_breach_notifications'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Disclosure subscription added successfully'
                ]);

        $representative->refresh();
        $this->assertContains('data_breach_notifications', $representative->disclosure_subscriptions);
    }

    /** @test */
    public function it_can_remove_disclosure_subscription()
    {
        $representative = Representative::factory()->create([
            'company_id' => $this->company->id,
            'disclosure_subscriptions' => ['gdpr_updates', 'data_breach_notifications']
        ]);

        $response = $this->postJson("/api/v1/representatives/{$representative->id}/remove-disclosure-subscription", [
            'disclosure_type' => 'data_breach_notifications'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Disclosure subscription removed successfully'
                ]);

        $representative->refresh();
        $this->assertNotContains('data_breach_notifications', $representative->disclosure_subscriptions);
    }

    /** @test */
    public function it_can_update_last_disclosure_date()
    {
        $representative = Representative::factory()->create(['company_id' => $this->company->id]);

        $response = $this->postJson("/api/v1/representatives/{$representative->id}/update-last-disclosure-date");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Last disclosure date updated successfully'
                ]);

        $representative->refresh();
        $this->assertNotNull($representative->last_disclosure_date);
    }

    /** @test */
    public function it_can_get_representatives_by_company()
    {
        Representative::factory()->count(2)->create(['company_id' => $this->company->id]);
        $otherCompany = Company::factory()->create();
        Representative::factory()->create(['company_id' => $otherCompany->id]);

        $response = $this->getJson("/api/v1/representatives/company/{$this->company->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Company representatives retrieved successfully'
                ]);

        $this->assertCount(2, $response->json('data'));
    }

    /** @test */
    public function it_can_get_disclosure_summary()
    {
        Representative::factory()->create([
            'company_id' => $this->company->id,
            'disclosure_subscriptions' => ['gdpr_updates', 'data_breach_notifications'],
            'is_active' => true
        ]);

        Representative::factory()->create([
            'company_id' => $this->company->id,
            'disclosure_subscriptions' => ['gdpr_updates'],
            'is_active' => false
        ]);

        $response = $this->getJson('/api/v1/representatives/disclosure-summary');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'total_representatives' => 2,
                        'active_representatives' => 1,
                        'total_subscriptions' => 3,
                        'representatives_with_subscriptions' => 2
                    ]
                ]);
    }

    /** @test */
    public function it_validates_required_fields_on_create()
    {
        $response = $this->postJson('/api/v1/representatives', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['company_id', 'first_name', 'last_name', 'email']);
    }

    /** @test */
    public function it_validates_unique_email()
    {
        $existingRepresentative = Representative::factory()->create(['company_id' => $this->company->id]);

        $response = $this->postJson('/api/v1/representatives', [
            'company_id' => $this->company->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $existingRepresentative->email
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }
}
