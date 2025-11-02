<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserLocation;
use App\Models\UserReferencePhone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users
        $users = User::whereIn('id', [1, 2, 3, 4, 5, 6, 7])->get();
        
        if ($users->isEmpty()) {
            $this->command->error('No users found in database. Please run migrations and seed users first.');
            return;
        }

        // Insert 10 locations
        $locations = [
            ['latitude' => 28.7041, 'longitude' => 77.1025], // Delhi
            ['latitude' => 19.0760, 'longitude' => 72.8777], // Mumbai
            ['latitude' => 12.9716, 'longitude' => 77.5946], // Bangalore
            ['latitude' => 22.5726, 'longitude' => 88.3639], // Kolkata
            ['latitude' => 18.5204, 'longitude' => 73.8567], // Pune
            ['latitude' => 17.3850, 'longitude' => 78.4867], // Hyderabad
            ['latitude' => 26.9124, 'longitude' => 75.7873], // Jaipur
            ['latitude' => 23.0225, 'longitude' => 72.5714], // Ahmedabad
            ['latitude' => 13.0827, 'longitude' => 80.2707], // Chennai
            ['latitude' => 25.5941, 'longitude' => 85.1376], // Patna
        ];

        $this->command->info('Inserting 10 location records...');
        foreach ($locations as $index => $location) {
            $user = $users->get($index % $users->count());
            UserLocation::create([
                'user_id' => $user->id,
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
            ]);
        }
        $this->command->info('✓ 10 location records inserted successfully!');

        // Insert 10 phone contacts
        $contacts = [
            ['name' => 'John Doe', 'contact_number' => '+91-9876543210'],
            ['name' => 'Jane Smith', 'contact_number' => '+91-9876543211'],
            ['name' => 'Rajesh Kumar', 'contact_number' => '+91-9876543212'],
            ['name' => 'Priya Sharma', 'contact_number' => '+91-9876543213'],
            ['name' => 'Amit Patel', 'contact_number' => '+91-9876543214'],
            ['name' => 'Neha Singh', 'contact_number' => '+91-9876543215'],
            ['name' => 'Karan Malhotra', 'contact_number' => '+91-9876543216'],
            ['name' => 'Sneha Verma', 'contact_number' => '+91-9876543217'],
            ['name' => 'Vikram Reddy', 'contact_number' => '+91-9876543218'],
            ['name' => 'Anjali Joshi', 'contact_number' => '+91-9876543219'],
        ];

        $this->command->info('Inserting 10 phone contact records...');
        foreach ($contacts as $index => $contact) {
            $user = $users->get($index % $users->count());
            UserReferencePhone::create([
                'user_id' => $user->id,
                'name' => $contact['name'],
                'contact_number' => $contact['contact_number'],
            ]);
        }
        $this->command->info('✓ 10 phone contact records inserted successfully!');

        $this->command->info('');
        $this->command->info('✓ Test data seeding completed successfully!');
    }
}
