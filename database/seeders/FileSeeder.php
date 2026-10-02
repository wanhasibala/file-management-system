<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        if (! $admin) {
            return;
        }

        // Ensure storage directory exists
        Storage::disk('public')->makeDirectory('documents');

        $deptIT = Department::where('code', 'IT')->first();
        $deptHR = Department::where('code', 'HR')->first();
        $deptFIN = Department::where('code', 'FIN')->first();
        $deptOPS = Department::where('code', 'OPS')->first();
        $deptLEG = Department::where('code', 'LEG')->first();
        $deptMKT = Department::where('code', 'MKT')->first();

        $folderAnnualReports = Folder::where('name', 'Annual Reports')->first();
        $folderSOP = Folder::where('name', 'Standard Operating Procedures')->first();
        $folderHandbook = Folder::where('name', 'Employee Handbook')->first();
        $folderFinancials = Folder::where('name', 'Quarterly Financials')->first();
        $folderBoard = Folder::where('name', 'Board Meeting Minutes')->first();

        // Sample PDF content (minimal valid PDF binary)
        $samplePdfContent = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << >> >>\nendobj\n4 0 obj\n<< /Length 44 >>\nstream\nBT\n/F1 18 Tf\n100 700 Td\n(Lion Group - Official Document) Tj\nET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\n0000000216 00000 n\ntrailer\n<< /Size 5 /Root 1 0 R >>\nstartxref\n310\n%%EOF";

        // Sample SVG content
        $sampleSvgContent = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300"><rect width="100%" height="100%" fill="#1e293b"/><circle cx="200" cy="150" r="80" fill="#3b82f6"/><text x="200" y="155" fill="#ffffff" font-size="20" font-family="sans-serif" text-anchor="middle" alignment-baseline="middle">Lion Group FMS</text></svg>';

        // Sample text content
        $sampleTextContent = "LION GROUP ARCHITECTURE & SECURITY POLICIES\n===========================================\n1. Zero Trust Architecture across all microservices\n2. Role-Based Access Control (RBAC) strictly enforced\n3. Daily automated snapshots and encrypted cold storage backup\n4. GDPR & Indonesian Personal Data Protection (UU PDP) compliant.\n";

        // Sample CSV content
        $sampleCsvContent = "Department,Employees,Q1_Budget,Q2_Budget,Status\nIT,45,250000,275000,Approved\nOperations,120,650000,700000,Approved\nHR,15,85000,90000,Approved\nFinance,22,120000,125000,Approved\n";

        $sampleFiles = [
            [
                'title' => 'Lion Group Annual Report FY 2025',
                'folder_id' => $folderAnnualReports?->id,
                'department_id' => $deptFIN?->id ?? 1,
                'file_name' => 'annual_report_fy2025.pdf',
                'path' => 'documents/annual_report_fy2025.pdf',
                'content' => $samplePdfContent,
                'mime_type' => 'application/pdf',
                'extension' => 'pdf',
                'description' => 'Comprehensive consolidated audited financial statements and performance metrics for fiscal year 2025.',
            ],
            [
                'title' => 'Flight Operations Safety Checklist SOP',
                'folder_id' => $folderSOP?->id,
                'department_id' => $deptOPS?->id ?? 1,
                'file_name' => 'flight_safety_sop_2026.pdf',
                'path' => 'documents/flight_safety_sop_2026.pdf',
                'content' => $samplePdfContent,
                'mime_type' => 'application/pdf',
                'extension' => 'pdf',
                'description' => 'Mandatory pre-flight and in-flight standard operating procedures and safety compliance protocol.',
            ],
            [
                'title' => 'Employee Code of Conduct & Benefits 2026',
                'folder_id' => $folderHandbook?->id,
                'department_id' => $deptHR?->id ?? 1,
                'file_name' => 'employee_handbook_2026.pdf',
                'path' => 'documents/employee_handbook_2026.pdf',
                'content' => $samplePdfContent,
                'mime_type' => 'application/pdf',
                'extension' => 'pdf',
                'description' => 'Official employee handbook covering workspace policies, ethics, health benefits, and compensation guidelines.',
            ],
            [
                'title' => 'Q1 Fiscal Budget Variance Report',
                'folder_id' => $folderFinancials?->id,
                'department_id' => $deptFIN?->id ?? 1,
                'file_name' => 'q1_budget_variance.csv',
                'path' => 'documents/q1_budget_variance.csv',
                'content' => $sampleCsvContent,
                'mime_type' => 'text/csv',
                'extension' => 'csv',
                'description' => 'Department-level budgetary breakdown and allocation analysis for Q1 2026.',
            ],
            [
                'title' => 'Corporate Brand Guidelines & Logo Asset',
                'folder_id' => null, // Root level file
                'department_id' => $deptMKT?->id ?? 1,
                'file_name' => 'brand_vector_asset.svg',
                'path' => 'documents/brand_vector_asset.svg',
                'content' => $sampleSvgContent,
                'mime_type' => 'image/svg+xml',
                'extension' => 'svg',
                'description' => 'Vector graphics brand icon and corporate identity specification assets.',
            ],
            [
                'title' => 'Enterprise Cloud Security & RBAC Guidelines',
                'folder_id' => null,
                'department_id' => $deptIT?->id ?? 1,
                'file_name' => 'it_security_policy.txt',
                'path' => 'documents/it_security_policy.txt',
                'content' => $sampleTextContent,
                'mime_type' => 'text/plain',
                'extension' => 'txt',
                'description' => 'Internal engineering document detailing security posture, cryptographic standards, and access control.',
            ],
            [
                'title' => 'Board Resolution 01-2026 Corporate Expansion',
                'folder_id' => $folderBoard?->id,
                'department_id' => $deptLEG?->id ?? 1,
                'file_name' => 'board_resolution_01_2026.pdf',
                'path' => 'documents/board_resolution_01_2026.pdf',
                'content' => $samplePdfContent,
                'mime_type' => 'application/pdf',
                'extension' => 'pdf',
                'description' => 'Signed resolution of the board regarding route expansion and fleet procurement strategy.',
            ],
        ];

        foreach ($sampleFiles as $f) {
            Storage::disk('public')->put($f['path'], $f['content']);
            $fileSize = Storage::disk('public')->size($f['path']);

            $fileRecord = File::updateOrCreate(
                ['file_name' => $f['file_name']],
                [
                    'title' => $f['title'],
                    'folder_id' => $f['folder_id'],
                    'department_id' => $f['department_id'],
                    'user_id' => $admin->id,
                    'file_path' => $f['path'],
                    'mime_type' => $f['mime_type'],
                    'file_size' => $fileSize,
                    'extension' => $f['extension'],
                    'description' => $f['description'],
                    'download_count' => rand(3, 42),
                ]
            );

            ActivityLog::create([
                'user_id' => $admin->id,
                'action' => 'uploaded',
                'entity_type' => 'File',
                'entity_id' => $fileRecord->id,
                'description' => "Uploaded file '{$fileRecord->title}' ({$fileRecord->file_name})",
                'properties' => [
                    'title' => $fileRecord->title,
                    'file_name' => $fileRecord->file_name,
                    'size' => $fileRecord->file_size,
                ],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (System Seeder)',
                'created_at' => now()->subMinutes(rand(10, 1440)),
            ]);
        }
    }
}
