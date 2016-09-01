<?php

$map = [["A", "B", 7],
    ["A", "C", 9],
    ["A", "F", 14],
    ["B", "C", 10],
    ["B", "D", 15],
    ["C", "F", 2],
    ["C", "D", 11],
    ["D", "E", 6],
    ["E", "F", 9]];

$o = new Dijkstra();
$result = $o->getTrack("A", "E", $map);
var_dump($result);


/**
 * Dijkstra’s algorithm
 */
class Dijkstra
{

    /**
	 * Get the cheapest way from one town to other
	 *
     * @param string $townA
     * @param string $townB
     * @param array $map
     * @return array
     * @throws Exception
     */
    public function getTrack($townA, $townB, array $map)
    {
        if( !$this->checkMapQuality($map) )
            throw new Exception("Wrong map");

        $towns = $this->convertTownNamesToDigits($map);
        $graph = $this->convertMapToGraph($map, $towns);

        $dist = []; // here will be stored prices from townA to all other
        $used = [];
        $parent = [];

        for ($i = 0; $i < count($towns); $i++) {
            $dist[$i] = PHP_INT_MAX;
            $used[$i] = false;
            $parent[$i] = -1;
        }

        $dist[$towns[$townA]] = 0;

        for ($i = 0; $i < count($towns); $i++) {
            $v = -1;
            for ($j = 0; $j < count($towns); $j++) {
                if (!$used[$j] && ($v == -1 || $dist[$j] < $dist[$v])) {
                    $v = $j;
                }
            }
            $used[$v] = true;
            for ($j = 0; $j < count($graph[$v]); $j++) {
                if ($graph[$v][$j] == 0) {
                    continue;
                }
                $u = $j;
                $w = $graph[$v][$j];
                if ($dist[$u] > $dist[$v] + $w) {
                    $dist[$u] = $dist[$v] + $w;
                    $parent[$u] = $v;
                }
            }
        }

        $path = $this->getShortestPath($townA, $townB, $towns, $parent);
        return ['price' => $dist[$towns[$townB]], 'path' => $path];
    }

    private function checkMapQuality(array $map)
    {
        $check = true;
        foreach ($map as $item) {
            if(count($item) != 3 || !is_int($item[2]) || $item[2] <= 0 ) {
                $check = false;
                break;
            }
        }
        return $check;
    }

    private function getShortestPath($townA, $townB, $towns, $parent)
    {
        $path = [$townB];
        $current = $towns[$townB];
        $townToDigit = array_flip($towns);
        $i = 0;
        while (true) {
            if ($i == $current) {
                if ($i == $parent[$i]) {
                    throw new Exception("Something goes wrong in path calculation");
                }
                $path[] = $townToDigit[$parent[$i]];
                $current = $parent[$i];
                if ($current == $towns[$townA])
                    break;
                $i = 0;
            } else {
                $i++;
            }
            if ($i >= count($parent))
                break;
        }
        $path = array_reverse($path);
        return $path;
    }

    private function convertTownNamesToDigits(array $map)
    {
        $towns = [];
        foreach ($map as $item) {
            if (!isset($towns[$item[0]])) {
                $i = count($towns);
                $towns[$item[0]] = $i;
            }
            if (!isset($towns[$item[1]])) {
                $i = count($towns);
                $towns[$item[1]] = $i;
            }
        }

        return $towns;
    }

    private function initZeroGraph($verticesNumber)
    {
        $graph = [];
        for ($i = 0; $i < $verticesNumber; $i++) {
            for ($j = 0; $j < $verticesNumber; $j++) {
                $graph[$i][$j] = 0;
            }
        }
        return $graph;
    }

    private function convertMapToGraph(array $map, array $towns)
    {
        $graph = $this->initZeroGraph(count($map));
        foreach ($map as $item) {
            $townA = $towns[$item[0]];
            $townB = $towns[$item[1]];
            $graph[$townA][$townB] = $item[2];
            $graph[$townB][$townA] = $item[2];
        }
        return $graph;
    }

}