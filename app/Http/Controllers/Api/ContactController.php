<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserReferencePhone;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Store multiple contacts for authenticated user (Bulk Insert)
     */
    public function storeBulk(Request $request)
    {
        $validated = $request->validate([
            'contacts' => 'required|array|min:1',
            'contacts.*.contact_number' => 'required|string|max:255',
            'contacts.*.name' => 'nullable|string|max:255',
        ]);

        // Get user ID from authenticated user
        $userId = $request->user()->id;

        // Delete existing contacts for this user
        UserReferencePhone::where('user_id', $userId)->delete();

        // Prepare contacts data
        $contactsData = [];
        foreach ($validated['contacts'] as $contact) {
            $contactsData[] = [
                'user_id' => $userId,
                'contact_number' => $contact['contact_number'],
                'name' => $contact['name'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk insert contacts
        UserReferencePhone::insert($contactsData);

        // Get the inserted contacts
        $insertedContacts = UserReferencePhone::where('user_id', $userId)->get();

        return response()->json([
            'success' => true,
            'message' => 'Contacts saved successfully',
            'data' => [
                'user_id' => $userId,
                'total_contacts' => count($insertedContacts),
                'contacts' => $insertedContacts
            ]
        ], 201);
    }
}
