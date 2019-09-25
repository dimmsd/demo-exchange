<?php

use Illuminate\Http\Request;

if (!function_exists('set_active_menu')) {
    function set_active_menu($route_name)
    {
        return (Route::currentRouteName() == $route_name) ? 'active' : '';
    }
}