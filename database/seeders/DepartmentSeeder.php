<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'IT Infrastructure, Systems, Security and Software Engineering',
                'color' => 'indigo',
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Talent Acquisition, Employee Relations, and Payroll',
                'color' => 'emerald',
            ],
            [
                'name' => 'Finance & Accounting',
                'code' => 'FIN',
                'description' => 'Financial Reporting, Budgeting, and Tax Compliance',
                'color' => 'amber',
            ],
            [
                'name' => 'Flight Operations',
                'code' => 'OPS',
                'description' => 'Aviation Flight Planning, Crew Rostering, and Safety Systems',
                'color' => 'sky',
            ],
            [
                'name' => 'Legal & Compliance',
                'code' => 'LEG',
                'description' => 'Regulatory Compliance, Contracts, and Corporate Governance',
                'color' => 'purple',
            ],
            [
                'name' => 'Marketing & Communications',
                'code' => 'MKT',
                'description' => 'Brand Strategy, Public Relations, and Commercial Campaigns',
                'color' => 'rose',
            ],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['code' => $dept['code']],
                $dept
            );
        }
    }
}
