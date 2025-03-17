<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DistanceController extends Controller
{
    public function getDistanceFurthestAway() {
        try {
            //init data
            $people = [];
            $result = [];
            $numPeople = 40;  // 
            for ($i = 1; $i <= $numPeople; $i++) {
                switch(true){
                    case $i <= 10:
                        $people[] = [
                            'name' => "Người $i",
                            'red'   => rand(0, 100) / 1.0,
                            'yellow'   => rand(0, 100) / 1.0
                        ];
                        break;
                    case $i > 10 && $i <= 20:
                        $people[] = [
                            'name' => "Người $i",
                            'red'   => rand(0, 100) / 1.0,
                            'green'   => rand(0, 100) / 1.0
                        ];
                        break;
                    case $i > 20 && $i <= 30:
                        $people[] = [
                            'name' => "Người $i",
                            'blue'   => rand(0, 100) / 1.0,
                            'yellow'   => rand(0, 100) / 1.0
                        ];
                        break;
                    case $i > 30 && $i <= 40:
                        $people[] = [
                            'name' => "Người $i",
                            'blue'   => rand(0, 100) / 1.0,
                            'green'   => rand(0, 100) / 1.0
                        ];
                        break;
                }
            }
            // calc min distance 
            foreach ($people as $i => $person) {
                $result[$i] = $coordinates = $this->getCoordinates($person);
                $result[$i]['name'] = $person["name"];
                $minDistance = INF; // init min distance

                // compare with others 
                foreach ($people as $j => $other) {
                    if ($i === $j) {
                        continue;
                    }
                    $otherCoordinates = $this->getCoordinates($other);
                    $dist = $this->calcDistance($coordinates, $otherCoordinates);
                    if ($dist < $minDistance) {
                        $minDistance = $dist;
                    }
                }

                $result[$i]['minDistance'] = $minDistance;
            }
    
            // sort by distance
            usort($result, function($a, $b) {
                return $b['minDistance'] <=> $a['minDistance'];
            });
    
            // Get 10% maximum distance
            $topCount = ceil(count($result) * 0.1);
            $maximumDistance = array_slice($result, 0, $topCount);

            return response()->json(["data" => $maximumDistance, "status" => true, "message" => "Success"]);
        } catch (\Throwable $th) {

            Log::debug($th->getMessage());
            return response()->json(["data" => [], "status" => true, "message" => "Failed"]);
        }
    }

    // calc distance 
    function calcDistance($p1, $p2) {
        return sqrt(pow($p1['X'] - $p2['X'], 2) + pow($p1['Y'] - $p2['Y'], 2));
    }

    //calc coordinates of point
    function getCoordinates($person){
        $coordinates = [];
        if(isset($person['red']) && isset($person['yellow'])){
            $coordinates = [
                "X" => - $person['red'],
                "Y" => $person['yellow'],
            ];
        }
        else if(isset($person['red']) && isset($person['green'])){
            $coordinates = [
                "X" => $person['green'],
                "Y" => $person['red'],
            ];
        }
        else if(isset($person['blue']) && isset($person['yellow'])){
            $coordinates = [
                "X" => -$person['blue'],
                "Y" => -$person['yellow'],
            ];
        }
        else if (isset($person['blue']) && isset($person['green'])){
            $coordinates = [
                "X" => $person['blue'],
                "Y" => - $person['green'],
            ];
        }

        return $coordinates;
    }
}
