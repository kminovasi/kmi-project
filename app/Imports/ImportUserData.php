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
            $employeeId = $row[0];
            $name = $row[1] . ' ' . $row[2]; // First Name + Last Name (spasi ditambahkan)
            $positionTitle = $row[4];
            $companyCode = $row[5];
            $companyName = $row[6];
            $directorate = $row[8];
            $groupHead = $row[10];
            $department = $row[12];
            $unit = $row[14];
            $section = $row[16];
            $subSection = $row[18];
            $birthDate = $row[19];
            $gender = $row[20];
            $bandLevel = $row[21];
            $rawEmail   = trim($row[22] ?? '');
            $managerId = $row[23];
            $contract = $row[25];
            $companyHome = $row[26];

            $email = $rawEmail !== '' ? $rawEmail : strtolower("{$employeeId}@sig.id");
            
            $skipEmail = [
                'AKHMAD.FARHAN@SIG.ID',
                'MUHAMMAD.FARIZAN@SIG.ID',
                'RIFQI.APRIAN@SIG.ID',
                'SAGI.SBI@SIG.ID',
                'zainul.a14153.vub@sig.id',
                'ERWIN.HALOMOANPURBA@SIG.ID',
                'PRI.AKBAR@SIG.ID',
                'eddy.syahputra@sig.id',
                'SYOFYAN.KAMAL@SIG.ID',
                'HARAJAKI.ASMARA@SIG.ID'
            ];

            if (in_array($email, $skipEmail)) {
                continue;
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
                'company_code' => $companyCode
            ];
                
            $user = User::where('employee_id', $employeeId)->first();
            if ($user) {
                // Update existing user
                $user->update($data);
            } else {
                // Create new user
                $data['employee_id'] = $employeeId;
                $data['uuid'] = Str::uuid()->toString();
                $data['password'] = Hash::make('test');
                User::create($data);
            }
        }
    }
}