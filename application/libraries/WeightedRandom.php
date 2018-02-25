<?php

/*
 * Example:
 * $values = array('A', 'B', 'C');
 * $weights = array(3, 7, 10);
 * 
 * echo weighted_random_simple($values, $weights);
 *   //Set up the lookups (once)
 *   list($lookup, $total_weight) = calc_lookups($weights);
 *   //....
 *   //Each time you need a weighted random selection :
 *   $val = weighted_random($values, $weights, $lookup, $total_weight);
 */

/**
 * Description of WeightedRandom
 *
 * @author vietbl
 */
class WeightedRandom {

    //put your code here
    public function __construct() {
        
    }

    /**
     * weighted_random()
     * Randomly select one of the elements based on their weights. Optimized for a large number of elements. 
     *
     * @param array $values Array of elements to choose from 
     * @param array $weights An array of weights. Weight must be a positive number.
     * @param array $lookup Sorted lookup array 
     * @param int $total_weight Sum of all weights
     * @return mixed Selected element
     */
    function weighted_random($values, $weights, $lookup = null, $total_weight = null) {
        if ($lookup == null) {
            list($lookup, $total_weight) = $this->calc_lookups($weights);
        }

        $r = mt_rand(0, $total_weight);
        return $values[$this->binary_search($r, $lookup)];
    }

    /**
     * calc_lookups()
     * Build the lookup array to use with binary search
     *
     * @param array $weights
     * @return array The lookup array and the sum of all weights
     */
    function calc_lookups($weights) {
        $lookup = array();
        $total_weight = 0;

        for ($i = 0; $i < count($weights); $i++) {
            $total_weight += $weights[$i];
            $lookup[$i] = $total_weight;
        }
        return array($lookup, $total_weight);
    }

    /**
     * binary_search()
     * Search a sorted array for a number. Returns the item's index if found. Otherwise 
     * returns the position where it should be inserted, or count($haystack)-1 if the
     * $needle is higher than every element in the array.
     *
     * @param int $needle
     * @param array $haystack
     * @return int
     */
    protected function binary_search($needle, $haystack) {
        $high = count($haystack) - 1;
        $low = 0;

        while ($low < $high) {
            $probe = (int) (($high + $low) / 2);
            if ($haystack[$probe] < $needle) {
                $low = $probe + 1;
            } else if ($haystack[$probe] > $needle) {
                $high = $probe - 1;
            } else {
                return $probe;
            }
        }

        if ($low != $high) {
            return $probe;
        } else {
            if ($haystack[$low] >= $needle) {
                return $low;
            } else {
                return $low + 1;
            }
        }
    }

    /**
     * weighted_random_simple()
     * Pick a random item based on weights.
     *
     * @param array $values Array of elements to choose from 
     * @param array $weights An array of weights. Weight must be a positive number.
     * @return mixed Selected element.
     */
    function weighted_random_simple($values, $weights) {
        $count = count($values);
        $i = 0;
        $n = 0;
        echo array_sum($weights);
        echo "<br>";
        $num = mt_rand(0, array_sum($weights));
        echo $num;
        echo "<br>";
        while ($i < $count) {
            $n += $weights[$i];
            if ($n >= $num) {
                break;
            }
            $i++;
        }
        return $values[$i];
    }

}
