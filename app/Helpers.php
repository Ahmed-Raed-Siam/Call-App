<?php


namespace App;


class Helpers
{

    public static function get_pageTitle()
    {
        $routeName = \Illuminate\Support\Facades\Route::currentRouteName();
        $count_dots = substr_count($routeName, '.');
        $routeNameArray = \Illuminate\Support\Str::of($routeName)->explode('.');
        $count = count($routeNameArray);
        $segment = 0;
        $page_name = '';
        if (($count === 3 || $count === 2) && $count_dots >= 2) {
            $segment = 1;
            $page_name = $routeNameArray[$segment];
        } elseif ($count === 4 && $count_dots >= 2) {
            $segment = 1;
            $page_name = $routeNameArray[$segment];
            $segment = 2;
            $page_name .= ' ' . $routeNameArray[$segment];
        } elseif ($count === 5 && $count_dots >= 2) {
            $segment = 1;
            $page_name = $routeNameArray[$segment];
            $segment = 2;
            $page_name .= ' ' . $routeNameArray[$segment];
            $segment = 3;
            $page_name .= ' ' . $routeNameArray[$segment];
        } elseif ($count === 2 && $count_dots === 1) {
            $segment = 0;
            $page_name = $routeNameArray[$segment];
        }
//        dd($routeName, $count_dots, $routeNameArray, $count, $routeNameArray[$segment], $page_name);
        return $page_name;
//        return print_r($routeNameArray);
//            return ['$routeNameArray' => $routeNameArray, 'count' => $count];
//        echo 'Route Name Array: ' . $routeNameArray . ' count_dots' . $count_dots . ' Count' . $count;
    }
}
