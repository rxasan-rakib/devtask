<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaskType;

class TaskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name'        => 'Software / App Development',
                'slug'        => 'software_development',
                'icon'        => 'code',
                'color'       => '#6366f1',
                'description' => 'Custom software and web application development tasks.',
                'order'       => 1,
                'is_active'   => true,
            ],
            [
                'name'        => 'Website Development',
                'slug'        => 'website_development',
                'icon'        => 'globe',
                'color'       => '#10b981',
                'description' => 'Frontend, corporate website, or ecommerce store creation.',
                'order'       => 2,
                'is_active'   => true,
            ],
            [
                'name'        => 'Bug Fix / Debugging',
                'slug'        => 'bug_fix',
                'icon'        => 'bug',
                'color'       => '#ef4444',
                'description' => 'Identifying and fixing errors, crashes, or unintended system behavior.',
                'order'       => 3,
                'is_active'   => true,
            ],
            [
                'name'        => 'API / Payment Integration',
                'slug'        => 'api_integration',
                'icon'        => 'plug',
                'color'       => '#8b5cf6',
                'description' => 'Integrating third-party REST APIs, payment gateways (bKash, SSLCommerz, etc.).',
                'order'       => 4,
                'is_active'   => true,
            ],
            [
                'name'        => 'POS System Setup',
                'slug'        => 'pos_setup',
                'icon'        => 'shopping-bag',
                'color'       => '#0ea5e9',
                'description' => 'Fresh POS installation, database setup, and initial environment config.',
                'order'       => 5,
                'is_active'   => true,
            ],
            [
                'name'        => 'POS Customization',
                'slug'        => 'pos_customization',
                'icon'        => 'sliders',
                'color'       => '#f59e0b',
                'description' => 'Custom POS invoice templates, branch reports, barcode changes, etc.',
                'order'       => 6,
                'is_active'   => true,
            ],
            [
                'name'        => 'System Update',
                'slug'        => 'system_update',
                'icon'        => 'refresh-cw',
                'color'       => '#3b82f6',
                'description' => 'Applying software patches, framework updates, or version upgrades.',
                'order'       => 7,
                'is_active'   => true,
            ],
            [
                'name'        => 'cPanel / Server Maintenance',
                'slug'        => 'cpanel_maintenance',
                'icon'        => 'server',
                'color'       => '#ec4899',
                'description' => 'Server health checks, SSL certificate renewal, backup & cPanel operations.',
                'order'       => 8,
                'is_active'   => true,
            ],
            [
                'name'        => 'Other / General',
                'slug'        => 'other',
                'icon'        => 'folder',
                'color'       => '#64748b',
                'description' => 'Miscellaneous work items or general technical assistance.',
                'order'       => 9,
                'is_active'   => true,
            ],
        ];

        foreach ($types as $type) {
            TaskType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }

        $this->command->info('✅ Task Types have been seeded successfully!');
    }
}
