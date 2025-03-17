<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgeController extends Controller
{
   public function calcAgeDifference(){

        //init data
        $ages = [18, 25, 30, 35, 40, 22, 10, 20, 27, 50, 48, 29, 55, 20, 60, 45, 70, 44, 41, 80];
        $totalPeople = count($ages);
        //calc average age
        $averageAge = array_sum($ages) / $totalPeople;

        // Calc the age differences and age Index 
        $ageDifferences = [];
        foreach ($ages as $key => $age) {
            $ageDifferences[] = [
                "ageIndex" => $key,
                "age" => $age,
                "ageDifference" => abs($age - $averageAge)
            ];
        }

        // Sort by age difference in descending order
        usort($ageDifferences, function($a, $b) {
            return $b['ageDifference'] <=> $a['ageDifference'];
        });

        $totalPeopleNeeded = (int) $totalPeople * 0.2;
        $result = array_slice($ageDifferences, 0, $totalPeopleNeeded);
        return response()->json(['result' => $result, 'status' => true], 200);
   }
}
