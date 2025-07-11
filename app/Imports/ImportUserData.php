<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportUserData implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public $successCount = 0;
    public $errorCount = 0;
    public $errors = [];

    public function collection(Collection $collection)
    {
        foreach ($collection->skip(1) as $index => $row) {
            try {
                $employeeId = $row[0] ?? '';
                $name = $row[1];
                $rawEmail   = trim($row[2] ?? '');
                $positionTitle = $row[3];
                $companyName = $row[4];
                $directorate = $row[5];
                $groupHead = $row[6];
                $department = $row[7];
                $unit = $row[8];
                $section = $row[9];
                $subSection = $row[10];
                $birthDate = $row[11];
                $gender = $row[12];
                $bandLevel = $row[13];
                $contract = $row[14];
                $companyHome = $row[15];
                $managerId = $row[16];
                $role = $row[17] ?? 'User';
                $companyCode = $row[18];
    
                $email = $rawEmail !== '' ? $rawEmail : strtolower("{$employeeId}@sig.id");
                
                if (empty($employeeId)) {
                    $employeeId = str_pad(random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
                }
    
                $data = [
                    'username' => $email,
                    'name' => $name,
                    'email' => $email,
                    'position_title' => $positionTitle,
                    'company_name' => $companyName,
                    'directorate_name' => $directorate,
                    'group_function_name' => $groupHead,
                    'department_name' => $department,
                    'unit_name' => $unit,
                    'section_name' => $section,
                    'sub_section_name' => $subSection,
                    'birth_of_date' => $birthDate,
                    'gender' => $gender,
                    'job_level' => $bandLevel,
                    'contract_type' => $contract,
                    'company_home' => $companyHome,
                    'manager_id' => $managerId,
                    'company_code' => $companyCode,
                    'role' => $role
                ];
                    
                $user = User::where('employee_id', $employeeId)->first();
                if ($user) {
                    $user->update($data);
                } else {
                    $data['uuid'] = Str::uuid()->toString();
                    $data['employee_id'] = $employeeId;
                    $data['password'] = Hash::make('test');
                    User::create($data);
                }
    
                $this->successCount++;
    
            } catch (\Throwable $e) {
                $this->errorCount++;
                $this->errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage(); // +2 karena skip header & 0-based
            }
        }
    }

}