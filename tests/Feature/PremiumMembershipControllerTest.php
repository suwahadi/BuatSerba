<?php

namespace Tests\Feature;

use App\Models\PremiumMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PremiumMembershipControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        $this->user = User::factory()->create();
    }

    public function test_show_purchase_page_unauthenticated(): void
    {
        $response = $this->get(route('premium.purchase'));

        $response->assertRedirect(route('login'));
    }

    public function test_show_purchase_page_authenticated(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('premium.purchase'));

        $response->assertStatus(200);
        $response->assertViewHas('price', 100000);
        $response->assertViewHas('activeMembership', null);
    }

    public function test_show_purchase_page_with_active_membership(): void
    {
        // Create active membership
        $membership = PremiumMembership::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addYear(),
        ]);
        
        $this->user->update(['premium_expires_at' => now()->addYear()]);

        $response = $this->actingAs($this->user)
            ->get(route('premium.purchase'));

        $response->assertStatus(200);
        $response->assertViewHas('activeMembership', $membership);
    }

    public function test_purchase_creates_pending_membership(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('premium.purchase.store'));

        $response->assertJson([
            'success' => true,
            'message' => 'Pembelian premium dibuat. Silakan upload bukti transfer.',
        ]);

        $this->assertDatabaseHas('premium_memberships', [
            'user_id' => $this->user->id,
            'status' => 'pending',
            'price' => 100000,
        ]);
    }

    public function test_purchase_fails_with_existing_pending(): void
    {
        // Create pending membership
        PremiumMembership::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('premium.purchase.store'));

        $response->assertJson(['success' => false]);
        $response->assertStatus(422);
    }

    public function test_upload_proof_successfully(): void
    {
        $membership = PremiumMembership::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $file = UploadedFile::fake()->image('proof.jpg');

        $response = $this->actingAs($this->user)
            ->post(route('premium.upload-proof'), [
                'membership_id' => $membership->id,
                'proof' => $file,
            ]);

        $response->assertJson([
            'success' => true,
            'message' => 'Bukti transfer berhasil diupload. Admin akan segera memverifikasi.',
        ]);

        $this->assertDatabaseHas('premium_memberships', [
            'id' => $membership->id,
        ]);

        Storage::disk('public')->assertExists($membership->refresh()->payment_proof_path);
    }

    public function test_upload_proof_fails_wrong_user(): void
    {
        $membership = PremiumMembership::factory()->create([
            'status' => 'pending',
        ]);

        $file = UploadedFile::fake()->image('proof.jpg');

        $response = $this->actingAs($this->user)
            ->post(route('premium.upload-proof'), [
                'membership_id' => $membership->id,
                'proof' => $file,
            ]);

        $response->assertStatus(403);
    }

    public function test_upload_proof_fails_non_pending(): void
    {
        $membership = PremiumMembership::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $file = UploadedFile::fake()->image('proof.jpg');

        $response = $this->actingAs($this->user)
            ->post(route('premium.upload-proof'), [
                'membership_id' => $membership->id,
                'proof' => $file,
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_my_memberships_page(): void
    {
        PremiumMembership::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('premium.memberships'));

        $response->assertStatus(200);
        $response->assertViewHas('memberships');
    }

    public function test_renew_membership_without_active(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('premium.renew'));

        $response->assertJson(['success' => false]);
        $response->assertStatus(422);
    }

    public function test_renew_membership_with_active(): void
    {
        $membership = PremiumMembership::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addYear(),
        ]);
        
        $this->user->update(['premium_expires_at' => now()->addYear()]);

        $response = $this->actingAs($this->user)
            ->post(route('premium.renew'));

        $response->assertJson(['success' => true]);
        
        // Should create new pending membership
        $this->assertDatabaseHas('premium_memberships', [
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);
    }
}
