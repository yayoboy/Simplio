<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $themes = [
            [
                'name' => 'Modern Light',
                'slug' => 'modern-light',
                'description' => 'A clean and modern light theme with subtle gradients',
                'is_global' => true,
                'design_tokens' => [
                    'colors' => [
                        'primary' => '#3B82F6',
                        'secondary' => '#8B5CF6',
                        'accent' => '#10B981',
                        'background' => '#FFFFFF',
                        'surface' => '#F9FAFB',
                        'text' => '#111827',
                        'text-secondary' => '#6B7280',
                    ],
                    'typography' => [
                        'font-family' => 'Inter, system-ui, sans-serif',
                        'font-size-base' => '16px',
                        'font-size-heading' => '2.5rem',
                        'font-weight-normal' => '400',
                        'font-weight-medium' => '500',
                        'font-weight-bold' => '700',
                        'line-height' => '1.6',
                    ],
                    'spacing' => [
                        'xs' => '0.5rem',
                        'sm' => '1rem',
                        'md' => '1.5rem',
                        'lg' => '2rem',
                        'xl' => '3rem',
                    ],
                    'radius' => [
                        'sm' => '0.375rem',
                        'md' => '0.5rem',
                        'lg' => '0.75rem',
                        'full' => '9999px',
                    ],
                    'shadow' => [
                        'sm' => '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                        'md' => '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                        'lg' => '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
                    ],
                ],
            ],
            [
                'name' => 'Dark Mode',
                'slug' => 'dark-mode',
                'description' => 'Professional dark theme optimized for readability',
                'is_global' => true,
                'design_tokens' => [
                    'colors' => [
                        'primary' => '#60A5FA',
                        'secondary' => '#A78BFA',
                        'accent' => '#34D399',
                        'background' => '#111827',
                        'surface' => '#1F2937',
                        'text' => '#F9FAFB',
                        'text-secondary' => '#D1D5DB',
                    ],
                    'typography' => [
                        'font-family' => 'Inter, system-ui, sans-serif',
                        'font-size-base' => '16px',
                        'font-size-heading' => '2.5rem',
                        'font-weight-normal' => '400',
                        'font-weight-medium' => '500',
                        'font-weight-bold' => '700',
                        'line-height' => '1.6',
                    ],
                    'spacing' => [
                        'xs' => '0.5rem',
                        'sm' => '1rem',
                        'md' => '1.5rem',
                        'lg' => '2rem',
                        'xl' => '3rem',
                    ],
                    'radius' => [
                        'sm' => '0.375rem',
                        'md' => '0.5rem',
                        'lg' => '0.75rem',
                        'full' => '9999px',
                    ],
                    'shadow' => [
                        'sm' => '0 1px 2px 0 rgba(0, 0, 0, 0.5)',
                        'md' => '0 4px 6px -1px rgba(0, 0, 0, 0.6)',
                        'lg' => '0 10px 15px -3px rgba(0, 0, 0, 0.7)',
                    ],
                ],
            ],
            [
                'name' => 'Minimal',
                'slug' => 'minimal',
                'description' => 'Ultra-minimal design with focus on typography',
                'is_global' => true,
                'design_tokens' => [
                    'colors' => [
                        'primary' => '#000000',
                        'secondary' => '#4B5563',
                        'accent' => '#F59E0B',
                        'background' => '#FFFFFF',
                        'surface' => '#FAFAFA',
                        'text' => '#000000',
                        'text-secondary' => '#6B7280',
                    ],
                    'typography' => [
                        'font-family' => 'Georgia, serif',
                        'font-size-base' => '18px',
                        'font-size-heading' => '3rem',
                        'font-weight-normal' => '400',
                        'font-weight-medium' => '500',
                        'font-weight-bold' => '700',
                        'line-height' => '1.8',
                    ],
                    'spacing' => [
                        'xs' => '0.75rem',
                        'sm' => '1.5rem',
                        'md' => '2rem',
                        'lg' => '3rem',
                        'xl' => '4rem',
                    ],
                    'radius' => [
                        'sm' => '0',
                        'md' => '0',
                        'lg' => '0',
                        'full' => '0',
                    ],
                    'shadow' => [
                        'sm' => 'none',
                        'md' => 'none',
                        'lg' => 'none',
                    ],
                ],
            ],
        ];

        foreach ($themes as $theme) {
            Theme::create($theme);
        }
    }
}
