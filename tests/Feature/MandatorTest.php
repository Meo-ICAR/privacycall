<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Mandator;
use App\Models\Company;

class MandatorTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test company
        $this->company = Company::factory()->create();
    }

    /** @test */
    public function it_can_list_mandators()
    {
        Mandator::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->getJson('/api/v1/mandators');

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
    public function it_can_create_a_mandator()
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
            'notes' => 'Test mandator'
        ];

        $response = $this->postJson('/api/v1/mandators', $data);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Mandator created successfully'
                ]);

        $this->assertDatabaseHas('mandators', [
            'email' => 'john.doe@example.com',
            'company_id' => $this->company->id
        ]);
    }

    /** @test */
    public function it_can_show_a_mandator()
    {
        $mandator = Mandator::factory()->create(['company_id' => $this->company->id]);

        $response = $this->getJson("/api/v1/mandators/{$mandator->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $mandator->id,
                        'email' => $mandator->email
                    ]
                ])
                ->assertJsonStructure([
                    'disclosure_summary'
                ]);
    }

    /** @test */
    public function it_can_update_a_mandator()
    {
        $mandator = Mandator::factory()->create(['company_id' => $this->company->id]);

        $updateData = [
            'first_name' => 'Updated Name',
            'position' => 'Updated Position',
            'is_active' => false
        ];

        $response = $this->putJson("/api/v1/mandators/{$mandator->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Mandator updated successfully'
                ]);

        $this->assertDatabaseHas('mandators', [
            'id' => $mandator->id,
            'first_name' => 'Updated Name',
            'position' => 'Updated Position',
            'is_active' => false
        ]);
    }

    /** @test */
    public function it_can_delete_a_mandator()
    {
        $mandator = Mandator::factory()->create(['company_id' => $this->company->id]);

        $response = $this->deleteJson("/api/v1/mandators/{$mandator->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Mandator deleted successfully'
                ]);

        $this->assertSoftDeleted('mandators', ['id' => $mandator->id]);
    }

    /** @test */
    public function it_can_add_disclosure_subscription()
    {
        $mandator = Mandator::factory()->create([
            'company_id' => $this->company->id,
            'disclosure_subscriptions' => ['gdpr_updates']
        ]);

        $response = $this->postJson("/api/v1/mandators/{$mandator->id}/add-disclosure-subscription", [
            'disclosure_type' => 'data_breach_notifications'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Disclosure subscription added successfully'
                ]);

        $mandator->refresh();
        $this->assertContains('data_breach_notifications', $mandator->disclosure_subscriptions);
    }

    /** @test */
    public function it_can_remove_disclosure_subscription()
    {
        $mandator = Mandator::factory()->create([
            'company_id' => $this->company->id,
            'disclosure_subscriptions' => ['gdpr_updates', 'data_breach_notifications']
        ]);

        $response = $this->postJson("/api/v1/mandators/{$mandator->id}/remove-disclosure-subscription", [
            'disclosure_type' => 'data_breach_notifications'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Disclosure subscription removed successfully'
                ]);

        $mandator->refresh();
        $this->assertNotContains('data_breach_notifications', $mandator->disclosure_subscriptions);
    }

    /** @test */
    public function it_can_update_last_disclosure_date()
    {
        $mandator = Mandator::factory()->create(['company_id' => $this->company->id]);

        $response = $this->postJson("/api/v1/mandators/{$mandator->id}/update-last-disclosure-date");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Last disclosure date updated successfully'
                ]);
    }

    /** @test */
    public function it_can_get_mandators_by_company()
    {
        Mandator::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->getJson("/api/v1/mandators/company/{$this->company->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Company mandators retrieved successfully'
                ])
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'company_id',
                            'first_name',
                            'last_name',
                            'email'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function it_can_get_disclosure_summary()
    {
        Mandator::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'disclosure_subscriptions' => ['gdpr_updates', 'data_breach_notifications']
        ]);

        $response = $this->getJson('/api/v1/mandators/disclosure-summary');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Disclosure summary retrieved successfully'
                ])
                ->assertJsonStructure([
                    'data' => [
                        'total_mandators',
                        'active_mandators',
                        'total_subscriptions',
                        'subscription_types',
                        'mandators_with_subscriptions'
                    ]
                ]);
    }

    /** @test */
    public function it_validates_required_fields_on_create()
    {
        $response = $this->postJson('/api/v1/mandators', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['company_id', 'first_name', 'email']);
    }

    /** @test */
    public function it_validates_unique_email()
    {
        $existingMandator = Mandator::factory()->create(['company_id' => $this->company->id]);

        $response = $this->postJson('/api/v1/mandators', [
            'company_id' => $this->company->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $existingMandator->email
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }
}
