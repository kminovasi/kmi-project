<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportUserData implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $employeeId = $row[1];
            $employeeName = $row[3];
            $employeeEmail = $row[12];
            $employeeDirectorate = $row[7];
            $employeeGroupHead = $row[8];
            $employeeDepartment = $row[9];
            $employeeUnit = $row[10];
            $employeeSection = $row[11];
            $employeeBandLevel = $row[5];
            $employeeCompanyName = $row[14];
            $employeeCompanyCode = $row[13];
            
            $skipEmail = [
                null, 
                '', 
                '-', 
                'GIRI.PRABOWO@SIG.ID', // Data Double
                'MUCHAMAD.SUPRIYADI@SIG.ID', // SF 800
                'RIFQI.APRIAN@SIG.ID', // SF 15734
                'AMY.AISYA@SIG.ID', // Data Double
                'ANIS@SIG.ID', // SF 5203
                'eddy.syahputra@sig.id', // SF 15767
                'AKHMAD.FARHAN@SIG.ID', // SF 15688
                'YOGI.PRASETYO@SIG.ID', // SF 15724 Email Double
                'MUHAMMAD.FARIZAN@SIG.ID', // SF 15689
                'SAGI.SBI@SIG.ID', // SF 15691
                'ZULHAMRI.PILIANG@SIG.ID', // SF 9004 Email Double
                'ERWIN.HALOMOANPURBA@SIG.ID', // SF 15731
                'SYOFYAN.KAMAL@SIG.ID', // SF 15697
                'HARAJAKI.ASMARA@SIG.ID' // SF 15690
            ];

            if (in_array($employeeEmail, $skipEmail)) {
                continue;
            }

            User::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                ],
                [
                    'name' => $employeeName,
                    'email' => $employeeEmail,
                    'username' => $employeeEmail,
                    'password' => Hash::make('test'),
                    'directorate_name' => $employeeDirectorate,
                    'group_function_name' => $employeeGroupHead,
                    'department_name' => $employeeDepartment,
                    'unit_name' => $employeeUnit,
                    'section_name' => $employeeSection,
                    'job_level' => $employeeBandLevel,
                    'company_name' => $employeeCompanyName,
                    'company_code' => $employeeCompanyCode
                ]
            );
        }
    }
}