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
    public function collection(Collection $collection)
    {
        foreach ($collection->skip(1) as $row) {
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

            
            if (empty($employeeId)) {
                $employeeId = str_pad(random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
            }

            $email = $rawEmail !== '' ? $rawEmail : strtolower("{$employeeId}@sig.id");
            
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
                'sub_section_of' => $subSection,
                'date_of_birth' => $this->dobToDdMmYyyy($birthDate),
                'gender' => $gender,
                'job_level' => $bandLevel,
                'contract_type' => $contract,
                'home_company' => $companyHome,
                'manager_id' => $managerId,
                'company_code' => $companyCode,
                'role' => $role
            ];
                
            $user = User::where('employee_id', $employeeId)->first();
            if ($user) {
                // Update existing user
                $user->update($data);
            } else {
                // Create new user
                $data['uuid'] = Str::uuid()->toString();
                $data['employee_id'] = $employeeId;
                $data['password'] = Hash::make('test');
                User::create($data);
            }
        }
    }

    private function dobToDdMmYyyy($v): ?string
    {
        if ($v === null) return null;
        if (is_string($v) && trim($v) === '') return null;

        if (is_numeric($v)) {
            try {
                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($v);
                return \Carbon\Carbon::instance($dt)->format('d/m/Y');
            } catch (\Throwable $e) { /* ignore */ }
        }

        $s = str_replace(['.', '\\'], '/', (string) $v);

        if (preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $s)) {
            return \Carbon\Carbon::parse($s)->format('d/m/Y');
        }

        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $s)) {
            [$a, $b, $c] = array_map('intval', explode('/', $s));
            $fmt = ($a > 12) ? 'd/m/Y' : (($b > 12) ? 'm/d/Y' : 'd/m/Y');
            return \Carbon\Carbon::createFromFormat($fmt, $s)->format('d/m/Y');
        }

        $sDash = str_replace('/', '-', (string) $v);
        if (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $sDash)) {
            return \Carbon\Carbon::createFromFormat('d-m-Y', $sDash)->format('d/m/Y');
        }

        try {
            return \Carbon\Carbon::parse((string)$v)->format('d/m/Y');
        } catch (\Throwable $e) {
            return null;
        }
    }


}