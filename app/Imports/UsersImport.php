<?php
namespace App\Imports;

// use App\User;
use App\Models\User as Users;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;

class UsersImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            Users::create([
                'name' => $row[1],
                'email' => $row[2],
                'password' => Hash::make($row[5]) ,

            ]);
        }
    }
}