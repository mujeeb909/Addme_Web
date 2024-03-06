<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\BusinessUser;

class UsersExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $Bp_Admins = BusinessUser::query()
        ->select(
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.username',
            'users.company_name',
            'users.company_friendly_name',
            'users.created_at as row_datetime',
            'business_users.domain',
            'business_users.account_limit',
            // Include account_limit column
            \DB::raw('CASE WHEN status = 1 THEN "Active" ELSE "Inactive" END as status'),
            \DB::raw('CASE
                        WHEN gender = 1 THEN "Male"
                        WHEN gender = 2 THEN "Female"
                        WHEN gender = 3 THEN "Unknown"
                        ELSE "Not Specified"
                    END as gender'),
        )
        ->leftJoin('users', 'users.id', '=', 'business_users.user_id')
        ->where('users.user_group_id', '=', '3')
        ->orderBy('company_name', 'ASC')
        ->get();
            return $Bp_Admins;
    }

    public function headings(): array
    {
        // Return column headings
        return [
            'First Name',
            'Last Name',
            'Email',
            'Username',
            'Company Name',
            'Company Friendly Name',
            'Created On',
            'Domain',
            'Account Limit',
            'Status',
            'Gender',
        ];
    }
}
