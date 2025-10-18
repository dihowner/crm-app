<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some products and customers
        $products = Product::take(5)->get();
        $customers = Customer::take(10)->get();

        // If no products exist, create some
        if ($products->isEmpty()) {
            $products = collect([
                Product::create([
                    'name' => 'Bose QuietComfort Earbuds',
                    'description' => 'Premium noise-cancelling wireless earbuds',
                    'price' => 249.99,
                    'stock_quantity' => 50,
                    'low_stock_threshold' => 10,
                    'status' => 'active',
                ]),
                Product::create([
                    'name' => 'Adidas Ultraboost 22',
                    'description' => 'High-performance running shoes',
                    'price' => 180.00,
                    'stock_quantity' => 25,
                    'low_stock_threshold' => 5,
                    'status' => 'active',
                ]),
                Product::create([
                    'name' => 'iPhone 15 Pro',
                    'description' => 'Latest iPhone with advanced features',
                    'price' => 999.99,
                    'stock_quantity' => 15,
                    'low_stock_threshold' => 3,
                    'status' => 'active',
                ]),
                Product::create([
                    'name' => 'Samsung Galaxy S24',
                    'description' => 'Premium Android smartphone',
                    'price' => 899.99,
                    'stock_quantity' => 20,
                    'low_stock_threshold' => 5,
                    'status' => 'active',
                ]),
                Product::create([
                    'name' => 'MacBook Air M3',
                    'description' => 'Lightweight laptop with M3 chip',
                    'price' => 1299.99,
                    'stock_quantity' => 8,
                    'low_stock_threshold' => 2,
                    'status' => 'active',
                ]),
            ]);
        }

        // If no customers exist, create some
        if ($customers->isEmpty()) {
            $customers = collect([
                Customer::create([
                    'name' => 'Ade Olu',
                    'phone' => '08102483872',
                    'email' => 'ade.olu@email.com',
                    'address' => '123 Victoria Island, Lagos',
                ]),
                Customer::create([
                    'name' => 'Jakes Test',
                    'phone' => '+123456782334',
                    'email' => 'jakes.test@email.com',
                    'address' => '456 Ikoyi, Lagos',
                ]),
                Customer::create([
                    'name' => 'Sarah Johnson',
                    'phone' => '08091234567',
                    'email' => 'sarah.j@email.com',
                    'address' => '789 Lekki Phase 1, Lagos',
                ]),
                Customer::create([
                    'name' => 'Michael Brown',
                    'phone' => '07012345678',
                    'email' => 'mike.brown@email.com',
                    'address' => '321 Surulere, Lagos',
                ]),
                Customer::create([
                    'name' => 'Grace Okafor',
                    'phone' => '09087654321',
                    'email' => 'grace.o@email.com',
                    'address' => '654 Gbagada, Lagos',
                ]),
                Customer::create([
                    'name' => 'David Wilson',
                    'phone' => '08123456789',
                    'email' => 'david.w@email.com',
                    'address' => '987 Magodo, Lagos',
                ]),
                Customer::create([
                    'name' => 'Fatima Ahmed',
                    'phone' => '07098765432',
                    'email' => 'fatima.a@email.com',
                    'address' => '147 Ikeja, Lagos',
                ]),
                Customer::create([
                    'name' => 'James Okafor',
                    'phone' => '08076543210',
                    'email' => 'james.o@email.com',
                    'address' => '258 Ajah, Lagos',
                ]),
                Customer::create([
                    'name' => 'Linda Martins',
                    'phone' => '09065432109',
                    'email' => 'linda.m@email.com',
                    'address' => '369 Festac, Lagos',
                ]),
                Customer::create([
                    'name' => 'Peter Okonkwo',
                    'phone' => '08134567890',
                    'email' => 'peter.o@email.com',
                    'address' => '741 Alaba, Lagos',
                ]),
            ]);
        }

        // Create sample orders with different statuses
        $orders = [
            // Unassigned orders (assigned_to = null)
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'quantity' => 1,
                'unit_price' => $products->random()->price,
                'total_price' => $products->random()->price,
                'status' => 'new',
                'assigned_to' => null, // Unassigned
                'order_number' => 'ORD-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'notes' => 'Customer requested express delivery',
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'quantity' => 2,
                'unit_price' => $products->random()->price,
                'total_price' => $products->random()->price * 2,
                'status' => 'new',
                'assigned_to' => null, // Unassigned
                'order_number' => 'ORD-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'notes' => 'Bulk order for office use',
                'created_at' => Carbon::now()->subHours(5),
            ],
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'quantity' => 1,
                'unit_price' => $products->random()->price,
                'total_price' => $products->random()->price,
                'status' => 'new',
                'assigned_to' => null, // Unassigned
                'order_number' => 'ORD-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'notes' => 'Customer prefers morning delivery',
                'created_at' => Carbon::now()->subHours(8),
            ],
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'quantity' => 1,
                'unit_price' => $products->random()->price,
                'total_price' => $products->random()->price,
                'status' => 'new',
                'assigned_to' => null, // Unassigned
                'order_number' => 'ORD-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'notes' => 'First-time customer',
                'created_at' => Carbon::now()->subHours(12),
            ],
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'quantity' => 1,
                'unit_price' => $products->random()->price,
                'total_price' => $products->random()->price,
                'status' => 'new',
                'assigned_to' => null, // Unassigned
                'order_number' => 'ORD-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'notes' => 'Customer mentioned urgent need',
                'created_at' => Carbon::now()->subHours(18),
            ],
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'quantity' => 1,
                'unit_price' => $products->random()->price,
                'total_price' => $products->random()->price,
                'status' => 'new',
                'assigned_to' => null, // Unassigned
                'order_number' => 'ORD-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'notes' => 'Weekend delivery preferred',
                'created_at' => Carbon::now()->subHours(24),
            ],

            // Assigned orders (assigned_to = user_id)
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'quantity' => 1,
                'unit_price' => $products->random()->price,
                'total_price' => $products->random()->price,
                'status' => 'scheduled',
                'assigned_to' => User::first()->id, // Assigned to first user
                'order_number' => 'ORD-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'notes' => 'Scheduled for tomorrow',
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'quantity' => 1,
                'unit_price' => $products->random()->price,
                'total_price' => $products->random()->price,
                'status' => 'delivered',
                'assigned_to' => User::first()->id, // Assigned to first user
                'order_number' => 'ORD-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'notes' => 'Successfully delivered',
                'created_at' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($orders as $orderData) {
            Order::create($orderData);
        }

        $this->command->info('Sample orders created successfully!');
        $this->command->info('Created ' . count($orders) . ' orders:');
        $this->command->info('- 6 unassigned orders (status: new)');
        $this->command->info('- 2 assigned orders (status: scheduled, delivered)');
    }
}
