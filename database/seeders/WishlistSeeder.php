<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 wishlist items for random users and products
        \App\Models\Wishlist::factory()
            ->count(50)
            ->create();
            
        // Create a specific test user with some wishlist items
        $testUser = \App\Models\User::factory()->create([
            'name' => 'Wishlist Tester',
            'email' => 'wishlist@example.com',
        ]);
        
        // Create 5 wishlist items for the test user
        \App\Models\Wishlist::factory()
            ->count(5)
            ->forUser($testUser)
            ->create();
            
        // Create a high-priority wishlist item for the test user
        \App\Models\Wishlist::factory()
            ->forUser($testUser)
            ->withPriority(5) // Highest priority
            ->create([
                'notes' => 'Really want this item!',
            ]);
    }
}
