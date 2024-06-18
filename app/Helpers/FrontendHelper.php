<?php

/**
 * Retrieves the value of the specified website name from the database.
 *
 * @param string $name The name of the website.
 * @throws Exception If the website does not exist.
 * @return mixed The value of the website.
 */
if (!function_exists('frontend_db')) {
    function frontend_db($name)
    {
        return \App\Models\Website::where('name', $name)->first()->value;
    }
}

if(!function_exists('app_url')) {
    function app_url($name)
    {
        return url('app' . $name);
    }
}

if(!function_exists('assets_url')) {
    function assets_url($name)
    {
        $image = str_replace('public/', '', $name);
        return asset('storage/' . $image);
    }
}

if(!function_exists('frontend')) {
    function frontend($var)
    {
        return asset('frontend/' . $var);
    }
}

if(!function_exists('pages')) {
    function pages($var)
    {
        return asset('landing/' . $var);
    }
}

/**
 * Display a SweetAlert popup based on the given type and message.
 *
 * @param string $type The type of the alert (e.g., 'danger', 'success').
 * @param string $msg The message to be displayed in the alert.
 * @return string The generated JavaScript code for the SweetAlert popup.
 */
if (!function_exists('swal_alert')) {
    function swal_alert($type, $msg)
    {
        switch($type)
        {
            case 'error':
                return "
                    <script>
                        Swal.fire(
                            'Woops..',
                            '$msg',
                            'error'
                        )
                    </script>
                ";
            break;
            
            case 'success':
                return "
                    <script>
                        Swal.fire(
                            'Success',
                            '$msg',
                            'success'
                        )
                    </script>
                ";
            break;
        }
    }
}

/**
 * Retrieves the "swal" session value.
 *
 * @return mixed The value of the "swal" session.
 */
if (!function_exists('swal_response')) {
    function swal_response()
    {
        if(session()->has('swal')) {
            return session('swal');
        }
    }
}