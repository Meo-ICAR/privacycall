<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CompanyEmail;
use App\Models\User;
use App\Services\EmailIntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyEmailTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $company;
    protected $emailService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');

        $this->company = Company::factory()->create([
            'data_controller_contact' => 'privacy@company.com',
        ]);

        $this->user->update(['company_id' => $this->company->id]);

        $this->emailService = app(EmailIntegrationService::class);

        Storage::fake('public');
    }

    /** @test */
    public function admin_can_view_company_emails_index()
    {
        $response = $this->actingAs($this->user)
            ->get(route('companies.emails.index', $this->company));

        $response->assertStatus(200);
        $response->assertViewIs('company-emails.index');
        $response->assertViewHas('company');
        $response->assertViewHas('emails');
        $response->assertViewHas('stats');
    }

    /** @test */
    public function admin_cannot_view_emails_for_other_companies()
    {
        $otherCompany = Company::factory()->create([
            'data_controller_contact' => 'privacy@other.com',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('companies.emails.index', $otherCompany));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_individual_email()
    {
        $email = CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('companies.emails.show', [$this->company, $email]));

        $response->assertStatus(200);
        $response->assertViewIs('company-emails.show');
        $response->assertViewHas('email');
        $response->assertViewHas('company');
    }

    /** @test */
    public function email_is_marked_as_read_when_viewed()
    {
        $email = CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'unread',
        ]);

        $this->actingAs($this->user)
            ->get(route('companies.emails.show', [$this->company, $email]));

        $this->assertDatabaseHas('company_emails', [
            'id' => $email->id,
            'status' => 'read',
        ]);
    }

    /** @test */
    public function admin_can_create_new_email()
    {
        $response = $this->actingAs($this->user)
            ->get(route('companies.emails.create', $this->company));

        $response->assertStatus(200);
        $response->assertViewIs('company-emails.create');
    }

    /** @test */
    public function admin_can_send_new_email()
    {
        $emailData = [
            'to_email' => 'recipient@example.com',
            'to_name' => 'John Doe',
            'subject' => 'Test Email',
            'body' => 'This is a test email body.',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('companies.emails.store', $this->company), $emailData);

        $response->assertRedirect(route('companies.emails.index', $this->company));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function admin_can_send_email_with_attachments()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $emailData = [
            'to_email' => 'recipient@example.com',
            'subject' => 'Test Email with Attachment',
            'body' => 'This is a test email with attachment.',
            'attachments' => [$file],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('companies.emails.store', $this->company), $emailData);

        $response->assertRedirect(route('companies.emails.index', $this->company));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function admin_can_reply_to_email()
    {
        $email = CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'read',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('companies.emails.reply', [$this->company, $email]));

        $response->assertStatus(200);
        $response->assertViewIs('company-emails.reply');
    }

    /** @test */
    public function admin_can_send_reply()
    {
        $email = CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'read',
        ]);

        $replyData = [
            'reply_body' => 'This is a reply to the email.',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('companies.emails.send-reply', [$this->company, $email]), $replyData);

        $response->assertRedirect(route('companies.emails.show', [$this->company, $email]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('company_emails', [
            'id' => $email->id,
            'status' => 'replied',
        ]);
    }

    /** @test */
    public function admin_can_update_email_status()
    {
        $email = CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'read',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('companies.emails.update', [$this->company, $email]), [
                'action' => 'archive',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('company_emails', [
            'id' => $email->id,
            'status' => 'archived',
        ]);
    }

    /** @test */
    public function admin_can_update_email_priority()
    {
        $email = CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
            'priority' => 'normal',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('companies.emails.update', [$this->company, $email]), [
                'action' => 'update_priority',
                'priority' => 'urgent',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('company_emails', [
            'id' => $email->id,
            'priority' => 'urgent',
        ]);
    }

    /** @test */
    public function admin_can_add_notes_to_email()
    {
        $email = CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $notes = 'This is an important note about the email.';

        $response = $this->actingAs($this->user)
            ->put(route('companies.emails.update', [$this->company, $email]), [
                'action' => 'add_notes',
                'notes' => $notes,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('company_emails', [
            'id' => $email->id,
            'notes' => $notes,
        ]);
    }

    /** @test */
    public function admin_can_filter_emails_by_status()
    {
        CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'unread',
        ]);

        CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'read',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('companies.emails.index', $this->company, ['status' => 'unread']));

        $response->assertStatus(200);
        $response->assertViewHas('emails');

        $emails = $response->viewData('emails');
        $this->assertEquals(1, $emails->count());
    }

    /** @test */
    public function admin_can_search_emails()
    {
        CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
            'subject' => 'GDPR Compliance Inquiry',
        ]);

        CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
            'subject' => 'General Inquiry',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('companies.emails.index', $this->company, ['search' => 'GDPR']));

        $response->assertStatus(200);
        $response->assertViewHas('emails');

        $emails = $response->viewData('emails');
        $this->assertEquals(1, $emails->count());
    }

    /** @test */
    public function admin_can_fetch_new_emails()
    {
        $response = $this->actingAs($this->user)
            ->post(route('companies.emails.fetch', $this->company));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    /** @test */
    public function admin_can_get_email_statistics()
    {
        CompanyEmail::factory()->count(5)->create([
            'company_id' => $this->company->id,
            'status' => 'unread',
        ]);

        CompanyEmail::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'status' => 'read',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('companies.emails.stats', $this->company));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'total' => 8,
                'unread' => 5,
                'read' => 3,
            ],
        ]);
    }

    /** @test */
    public function admin_can_delete_email()
    {
        $email = CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('companies.emails.destroy', [$this->company, $email]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseMissing('company_emails', [
            'id' => $email->id,
        ]);
    }

    /** @test */
    public function email_service_can_fetch_emails_for_company()
    {
        $result = $this->emailService->fetchEmailsForCompany($this->company);

        $this->assertTrue($result['success']);
        $this->assertGreaterThan(0, $result['processed']);
    }

    /** @test */
    public function email_service_can_get_statistics()
    {
        CompanyEmail::factory()->count(3)->create([
            'company_id' => $this->company->id,
        ]);

        $stats = $this->emailService->getEmailStats($this->company);

        $this->assertEquals(3, $stats['total']);
        $this->assertArrayHasKey('unread', $stats);
        $this->assertArrayHasKey('read', $stats);
        $this->assertArrayHasKey('replied', $stats);
    }

    /** @test */
    public function email_model_has_correct_relationships()
    {
        $email = CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertInstanceOf(Company::class, $email->company);
        $this->assertInstanceOf(User::class, $email->user);
        $this->assertEquals($this->company->id, $email->company->id);
        $this->assertEquals($this->user->id, $email->user->id);
    }

    /** @test */
    public function email_model_has_correct_accessors()
    {
        $email = CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
            'from_name' => 'John Doe',
            'from_email' => 'john@example.com',
        ]);

        $this->assertEquals('John Doe', $email->sender_display_name);
        $this->assertIsString($email->age);
        $this->assertIsString($email->excerpt);
        $this->assertIsInt($email->attachment_count);
    }

    /** @test */
    public function email_model_scopes_work_correctly()
    {
        CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'unread',
        ]);

        CompanyEmail::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'read',
        ]);

        $this->assertEquals(1, CompanyEmail::unread()->count());
        $this->assertEquals(1, CompanyEmail::read()->count());
        $this->assertEquals(2, CompanyEmail::byCompany($this->company->id)->count());
    }
}
