<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    function getStudentOutOfAverageAge(){
        try {
            // init data
            $data = [];
            $result = [];
            $totalClass = 20;
            $classInfo = [
                4 => 40,
                5 => 35,
                6 => 45,
                10 => 30
            ];
    
            for($i = 0; $i < $totalClass; $i++){
                $row["className"] = "Class $i";
                switch(true){
                    case $i < 5:
                        $row["class"] = 4;
                        break;
                    case $i < 10:
                        $row["class"] = 5;
                        break;
                    case $i < 15:
                        $row["class"] = 6;
                        break;
                    case $i < 20:
                        $row["class"] = 10;
                        break;
                }
                for($j = 0; $j < $classInfo[$row["class"]]; $j++){
                    $row['students'][$j]['years'] = rand(20, 22);
                    $row['students'][$j]['month'] = rand(0, 12);
                }
    
                $data[] = $row;
            }
    
            // Convert average age to months
            $averageAgeYears = 20;
            $averageAgeMonths = 8;
            $averageAgeInMonths = $this->convertAgeYearToMonth($averageAgeYears, $averageAgeMonths);
            
            // Define the range (6 months below and above) (Tính giới hạn cần tìm)
            $lowerThreshold = $averageAgeInMonths - 6; // 20 years 2 months
            $upperThreshold = $averageAgeInMonths + 6; // 20 years 14 months = 21 years 2 months

            foreach($data as $key => $item){
                $rec["className"] = $item['className'];
                $rec['studentOutOfAverageAge'] = $this->countStudentOutOfAverageAge($item['students'], $lowerThreshold, $upperThreshold);
                $result[] = $rec;
            }
    
            return response()->json(["data" => $result, "status" => true, "message" => "Success"]);
        } catch (\Throwable $th) {

            Log::debug($th->getMessage());
            return response()->json(["data" => [], "status" => false, "message" => "Failed"]);
        }
    }

    // Simulate random ages for students (for simplicity)
    function convertAgeYearToMonth($years, $month) {
        $averageAgeInMonths = ($years * 12) + $month;
        return $averageAgeInMonths;
    }

    // Count students larger or smaller than the threshold
    function countStudentOutOfAverageAge($students, $lowerThreshold, $upperThreshold) {
        $countStudent = 0;
        foreach ($students as $student) {
            $ageMonth = $this->convertAgeYearToMonth($student['years'], $student['month']);
            if($ageMonth < $lowerThreshold || $ageMonth > $upperThreshold){
                $countStudent++;
            }
        }

        return $countStudent;
    }
}
