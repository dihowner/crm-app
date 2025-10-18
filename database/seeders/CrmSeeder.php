<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\ProductForm;
use Illuminate\Support\Facades\Hash;

class CrmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample users
        $adminRole = Role::where('slug', 'admin')->first();
        $csrRole = Role::where('slug', 'csr')->first();
        $logisticRole = Role::where('slug', 'logistic_manager')->first();

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
            ]
        );

        // Create CSR users
        $csr1 = User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Smith',
                'password' => Hash::make('password'),
                'role_id' => $csrRole->id,
            ]
        );

        $csr2 = User::firstOrCreate(
            ['email' => 'mike@example.com'],
            [
                'name' => 'Mike Davis',
                'password' => Hash::make('password'),
                'role_id' => $csrRole->id,
            ]
        );

        $csr3 = User::firstOrCreate(
            ['email' => 'david@example.com'],
            [
                'name' => 'David Brown',
                'password' => Hash::make('password'),
                'role_id' => $csrRole->id,
            ]
        );

        // Create Logistic Manager
        $logisticManager = User::firstOrCreate(
            ['email' => 'mathew@example.com'],
            [
                'name' => 'Mathew Jonny',
                'password' => Hash::make('password'),
                'role_id' => $logisticRole->id,
            ]
        );

        // Create sample customers
        $customers = [
            [
                'name' => 'Jakes test',
                'phone' => '+123456782334',
                'email' => 'jakes@example.com',
                'state' => 'Abia',
                'address' => 'Dev test no 30 unites state, Abia, Nigeria',
                'status' => 'active',
                'total_orders' => 1,
                'total_spent' => 195000.00,
            ],
            [
                'name' => 'Ibrahim Eze',
                'phone' => '+2349685155670',
                'email' => 'ibrahim@example.com',
                'state' => 'Lagos',
                'address' => 'Victoria Island, Lagos, Nigeria',
                'status' => 'active',
                'total_orders' => 2,
                'total_spent' => 110000.00,
            ],
            [
                'name' => 'Segun Aliyu',
                'phone' => '+2348730295181',
                'email' => 'segun@example.com',
                'state' => 'Enugu',
                'address' => 'Enugu North, Enugu, Nigeria',
                'status' => 'active',
                'total_orders' => 3,
                'total_spent' => 775000.00,
            ],
            [
                'name' => 'Kemi Okafor',
                'phone' => '+2349642505910',
                'email' => 'kemi@example.com',
                'state' => 'Rivers',
                'address' => 'Port Harcourt, Rivers, Nigeria',
                'status' => 'active',
                'total_orders' => 1,
                'total_spent' => 250000.00,
            ],
            [
                'name' => 'Fatima Mohammed',
                'phone' => '+2347296037341',
                'email' => 'fatima@example.com',
                'state' => 'Kano',
                'address' => 'Kano Central, Kano, Nigeria',
                'status' => 'active',
                'total_orders' => 1,
                'total_spent' => 45000.00,
            ],
            [
                'name' => 'Tunde Ogbonna',
                'phone' => '+2348135804028',
                'email' => 'tunde@example.com',
                'state' => 'Enugu',
                'address' => 'Enugu South, Enugu, Nigeria',
                'status' => 'active',
                'total_orders' => 4,
                'total_spent' => 2749958.00,
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::firstOrCreate(
                ['phone' => $customerData['phone']],
                $customerData
            );
        }

        // Create sample orders
        $products = \App\Models\Product::all();
        $agents = \App\Models\Agent::all();
        $customers = Customer::all();

        $sampleOrders = [
            [
                'customer_id' => $customers[0]->id,
                'product_id' => $products[0]->id, // Adidas Ultraboost 22
                'assigned_to' => $admin->id,
                'agent_id' => $agents[2]->id, // Henry Logistics
                'quantity' => 3,
                'unit_price' => 65000.00,
                'total_price' => 195000.00,
                'status' => 'new',
                'order_number' => 'ORD-' . str_pad(161, 6, '0', STR_PAD_LEFT),
            ],
            [
                'customer_id' => $customers[1]->id,
                'product_id' => $products[1]->id, // Bose QuietComfort Earbuds
                'assigned_to' => $csr2->id,
                'agent_id' => $agents[1]->id, // Optimal Logistics
                'quantity' => 1,
                'unit_price' => 45000.00,
                'total_price' => 45000.00,
                'status' => 'scheduled',
                'scheduled_delivery_date' => now()->subDays(10),
                'order_number' => 'ORD-' . str_pad(162, 6, '0', STR_PAD_LEFT),
            ],
            [
                'customer_id' => $customers[1]->id,
                'product_id' => $products[0]->id, // Adidas Ultraboost 22
                'assigned_to' => $csr1->id,
                'agent_id' => $agents[0]->id, // Fresh Delivery
                'quantity' => 1,
                'unit_price' => 65000.00,
                'total_price' => 65000.00,
                'status' => 'scheduled',
                'scheduled_delivery_date' => now()->subDays(6),
                'order_number' => 'ORD-' . str_pad(163, 6, '0', STR_PAD_LEFT),
            ],
            [
                'customer_id' => $customers[2]->id,
                'product_id' => $products[2]->id, // Dell XPS 13
                'assigned_to' => $csr1->id,
                'agent_id' => $agents[1]->id, // Optimal Logistics
                'quantity' => 1,
                'unit_price' => 750000.00,
                'total_price' => 750000.00,
                'status' => 'delivered',
                'order_number' => 'ORD-' . str_pad(164, 6, '0', STR_PAD_LEFT),
            ],
            [
                'customer_id' => $customers[3]->id,
                'product_id' => $products[3]->id, // PlayStation 5
                'assigned_to' => $csr2->id,
                'agent_id' => $agents[0]->id, // Fresh Delivery
                'quantity' => 1,
                'unit_price' => 250000.00,
                'total_price' => 250000.00,
                'status' => 'delivered',
                'order_number' => 'ORD-' . str_pad(165, 6, '0', STR_PAD_LEFT),
            ],
        ];

        foreach ($sampleOrders as $orderData) {
            Order::firstOrCreate(
                ['order_number' => $orderData['order_number']],
                $orderData
            );
        }

        // Create sample inventory
        foreach ($products as $product) {
            foreach ($agents as $agent) {
                Inventory::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'agent_id' => $agent->id,
                    ],
                    [
                        'quantity' => rand(50, 800),
                        'low_stock_threshold' => 10,
                        'cost_price' => $product->price * 0.7, // 70% of selling price
                        'selling_price' => $product->price,
                    ]
                );
            }
        }

        // Create sample product forms
        $sampleProductForms = [
            [
                'form_name' => 'Organic Site 1',
                'product_id' => $products[0]->id, // Action Bitters
                'redirect_url' => 'https://organic-site.com/thank-you',
                'button_text' => 'ORDER NOW',
                'packages' => [
                    ['id' => 1, 'name' => '1 pack (Recommended)', 'price' => 10000.00, 'quantity' => 1],
                    ['id' => 2, 'name' => '2 pack (Best Value)', 'price' => 18000.00, 'quantity' => 2],
                ],
                'is_active' => true,
            ],
            [
                'form_name' => 'Website 2',
                'product_id' => $products[1]->id, // Bose QuietComfort Earbuds
                'redirect_url' => 'https://website2.com/success',
                'button_text' => 'Place Order',
                'packages' => [
                    ['id' => 1, 'name' => 'Single Unit', 'price' => 85000.00, 'quantity' => 1],
                    ['id' => 2, 'name' => 'Bundle Deal', 'price' => 150000.00, 'quantity' => 2],
                ],
                'is_active' => true,
            ],
            [
                'form_name' => 'Order Form',
                'product_id' => $products[2]->id, // PlayStation 5
                'redirect_url' => 'https://domain.com/order-success',
                'button_text' => 'BUY NOW',
                'packages' => [
                    ['id' => 1, 'name' => 'PS5 Console Only', 'price' => 250000.00, 'quantity' => 1],
                    ['id' => 2, 'name' => 'PS5 + Controller Bundle', 'price' => 280000.00, 'quantity' => 1],
                ],
                'is_active' => true,
            ],
            [
                'form_name' => 'Website A Form',
                'product_id' => $products[2]->id, // PlayStation 5
                'redirect_url' => 'https://website-a.com/thank-you',
                'button_text' => 'ORDER NOW',
                'packages' => [
                    ['id' => 1, 'name' => 'Standard Edition', 'price' => 250000.00, 'quantity' => 1],
                    ['id' => 2, 'name' => 'Digital Edition', 'price' => 230000.00, 'quantity' => 1],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($sampleProductForms as $formData) {
            $productForm = ProductForm::firstOrCreate(
                ['form_name' => $formData['form_name']],
                $formData
            );

            // Generate the form HTML for each product form
            $generatedForm = $productForm->generateFormHtml();
            $productForm->update(['generated_form' => $generatedForm]);
        }

        $this->command->info('CRM sample data seeded successfully!');
        $this->command->info('Admin Login: admin@example.com / admin123');
        $this->command->info('CSR Login: john@example.com / password');
        $this->command->info('Logistic Manager Login: mathew@example.com / password');
    }
}
