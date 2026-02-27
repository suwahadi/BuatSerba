<?php

namespace App\Http\Controllers;

use App\Models\PremiumMembership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PremiumMembershipController extends Controller
{
    /**
     * Show premium membership purchase page
     */
    public function showPurchase()
    {
        $user = Auth::user();
        
        // Get active membership if any
        $activeMembership = $user->activePremiumMembership()->first();
        
        return view('premium.purchase', [
            'price' => 100000,
            'activeMembership' => $activeMembership,
            'daysRemaining' => $activeMembership?->daysRemaining(),
        ]);
    }

    /**
     * Create a new premium membership purchase (pending state)
     */
    public function purchase(Request $request)
    {
        $user = Auth::user();

        // Check if user already has pending or active membership
        $existingPending = $user->premiumMemberships()
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki pembelian premium yang menunggu verifikasi. Silakan upload bukti transfer terlebih dahulu.',
            ], 422);
        }

        // Create new pending membership
        $membership = $user->premiumMemberships()->create([
            'price' => 100000,
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembelian premium dibuat. Silakan upload bukti transfer.',
            'membership_id' => $membership->id,
        ]);
    }

    /**
     * Upload payment proof
     */
    public function uploadProof(Request $request)
    {
        $request->validate([
            'membership_id' => 'required|exists:premium_memberships,id',
            'proof' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        $membership = PremiumMembership::findOrFail($request->membership_id);
        $user = Auth::user();

        // Ensure user owns this membership
        if ($membership->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Only pending memberships can upload proof
        if ($membership->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pembelian yang pending yang bisa upload bukti transfer.',
            ], 422);
        }

        try {
            // Delete old proof if exists
            if ($membership->payment_proof_path) {
                Storage::disk('public')->delete($membership->payment_proof_path);
            }

            // Store new proof
            $path = $request->file('proof')->store('premium-proof', 'public');

            // Update membership with proof path
            $membership->update(['payment_proof_path' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti transfer berhasil diupload. Admin akan segera memverifikasi.',
                'proof_path' => $path,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload bukti: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's premium memberships
     */
    public function myMemberships()
    {
        $user = Auth::user();
        $memberships = $user->premiumMemberships()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('premium.memberships', [
            'memberships' => $memberships,
            'activeMembership' => $user->activePremiumMembership()->first(),
        ]);
    }

    /**
     * Renew premium membership (create new one)
     */
    public function renew(Request $request)
    {
        $user = Auth::user();

        // Check if user has active membership to renew
        $activeMembership = $user->activePremiumMembership()->first();

        if (!$activeMembership) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki membership premium aktif untuk diperpanjang.',
            ], 422);
        }

        // Check if already has pending renewal
        $pendingRenewal = $user->premiumMemberships()
            ->where('status', 'pending')
            ->where('created_at', '>', now()->subDay())
            ->first();

        if ($pendingRenewal) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki perpanjangan membership yang menunggu verifikasi.',
            ], 422);
        }

        // Create new membership for renewal
        $newMembership = $user->premiumMemberships()->create([
            'price' => 100000,
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perpanjangan membership dibuat. Silakan upload bukti transfer.',
            'membership_id' => $newMembership->id,
        ]);
    }
}
