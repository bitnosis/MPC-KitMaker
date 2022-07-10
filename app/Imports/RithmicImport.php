<?php 

namespace App\Imports;

use App\Trade;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class RithmicImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
           /*User::create([
                'name' => $row[0],
            ]);*/


            print_r($row);
            echo "<br>";
        }
    }
}