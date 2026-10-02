<?php

namespace Database\Seeders;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Seeder;

class FolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        // Level 1: Root folders
        $corporate = Folder::firstOrCreate(
            ['name' => 'Corporate Documents', 'parent_id' => null],
            [
                'user_id' => $admin?->id,
                'description' => 'Official corporate governance, charters, and annual reports',
                'color' => 'blue',
            ]
        );

        $operations = Folder::firstOrCreate(
            ['name' => 'Flight Operations & Safety', 'parent_id' => null],
            [
                'user_id' => $admin?->id,
                'description' => 'Aviation manuals, safety checklists, and operating standard procedures',
                'color' => 'sky',
            ]
        );

        $hr = Folder::firstOrCreate(
            ['name' => 'Human Resources & Talent', 'parent_id' => null],
            [
                'user_id' => $admin?->id,
                'description' => 'Employee guidelines, handbooks, training curriculum, and onboarding',
                'color' => 'emerald',
            ]
        );

        $finance = Folder::firstOrCreate(
            ['name' => 'Finance & Accounting', 'parent_id' => null],
            [
                'user_id' => $admin?->id,
                'description' => 'Audited financial reports, tax filings, and fiscal budgets',
                'color' => 'amber',
            ]
        );

        // Level 2: Child folders
        $annualReports = Folder::firstOrCreate(
            ['name' => 'Annual Reports', 'parent_id' => $corporate->id],
            [
                'user_id' => $admin?->id,
                'description' => 'Audited annual reports for stakeholders and shareholders',
                'color' => 'blue',
            ]
        );

        Folder::firstOrCreate(
            ['name' => 'Board Meeting Minutes', 'parent_id' => $corporate->id],
            [
                'user_id' => $admin?->id,
                'description' => 'Minutes and resolutions from quarterly board meetings',
                'color' => 'purple',
            ]
        );

        // Level 3: Deeply nested folder to demonstrate unlimited hierarchical depth
        Folder::firstOrCreate(
            ['name' => 'Fiscal Year 2025', 'parent_id' => $annualReports->id],
            [
                'user_id' => $admin?->id,
                'description' => 'Comprehensive reports for fiscal year 2025',
                'color' => 'indigo',
            ]
        );

        Folder::firstOrCreate(
            ['name' => 'Standard Operating Procedures', 'parent_id' => $operations->id],
            [
                'user_id' => $admin?->id,
                'description' => 'Aviation and ground crew operational procedures',
                'color' => 'teal',
            ]
        );

        Folder::firstOrCreate(
            ['name' => 'Employee Handbook', 'parent_id' => $hr->id],
            [
                'user_id' => $admin?->id,
                'description' => 'Company policies, benefits, and conduct codes',
                'color' => 'emerald',
            ]
        );

        Folder::firstOrCreate(
            ['name' => 'Quarterly Financials', 'parent_id' => $finance->id],
            [
                'user_id' => $admin?->id,
                'description' => 'Quarterly budget variances and revenue summaries',
                'color' => 'amber',
            ]
        );
    }
}
