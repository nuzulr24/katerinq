<?php


if (!function_exists('isAttachment')) {
    /**
     * Determines if the given image is an attachment.
     *
     * @param string $image The path of the image file.
     * @return void
     */
    function isAttachment($image){
        $extension = pathinfo($image, PATHINFO_EXTENSION);
        $imgExtArr = ['jpg', 'jpeg', 'png'];
        if(in_array($extension, $imgExtArr)){
            echo 'images';
        } elseif(in_array($extension, ['xls','xlsx'])) {
            echo 'excel';
        } else {
            echo 'word';
        }
    }
}

if(!function_exists('app_info'))
{
    /**
     * Retrieves the website configuration for the specified name.
     *
     * @param string $name The name of the website.
     * @throws \Some_Exception_Class Description of the exception that can be thrown.
     * @return mixed The website configuration.
     */
    function app_info($name)
    {
        $websiteConfiguration = \App\Models\Website::where('name', $name)->first()->value;
        return $websiteConfiguration;
    }
}

if(!function_exists('app_smtp_info'))
{
    /**
     * Retrieves the website configuration for the specified name.
     *
     * @param string $name The name of the website.
     * @throws \Some_Exception_Class Description of the exception that can be thrown.
     * @return mixed The website configuration.
     */
    function app_smtp_info()
    {
        $mailConfiguration = \App\Models\Mail::find(1)->first();
        return $mailConfiguration;
    }
}

if(!function_exists('hasExpired'))
{
    /**
     * Determines if the given date has expired.
     *
     * @param string $date The date to check.
     * @return bool True if the date has expired, false otherwise.
     */
    function hasExpired($accountCreatedAt)
    {
        // Konversi tanggal pembuatan akun menjadi objek DateTime
        $accountCreatedTime = new DateTime($accountCreatedAt);
        $currentTime = new DateTime();

        // Hitung selisih waktu antara tanggal pembuatan akun dan waktu saat ini
        $interval = $accountCreatedTime->diff($currentTime);

        // Periksa jika selisih waktu lebih dari atau sama dengan 60 menit (3600 detik)
        return $interval->format('%i') >= 60;
    }
}

if(!function_exists('checkExpired'))
{
    function checkExpired($createdAt, $isExpired)
    {
        $givenDate = new DateTime($createdAt);
        $currentDate = new DateTime();

        // Add 3 days to the current date
        $currentDate->modify('+3 days');
        // Compare the given date timestamp with the current date + 3 days timestamp
        $isExpired = $givenDate > $currentDate;
        return $isExpired;
    }
}

/**
 * Generate an application URL.
 *
 * @param string $url The URL to append to the application URL.
 * @return string The generated application URL.
 */
if (!function_exists('app_url')) {
    function app_url($url)
    {
        return url('app/' . $url);
    }
}

if (!function_exists('site_url')) {
    function site_url($type, $url)
    {
        switch($type)
        {
            case 'seller':
                return url('seller/' . $url);
            break;

            case 'user':
                return url('user/' . $url);
            break;
        }
    }
}

if(!function_exists('removeUrlPrefix')) {
    function removeUrlPrefix($url)
    {
        $url = preg_replace('#^https?://#', '', $url);
        // Remove www. prefix
        $url = preg_replace('#^www\.#', '', $url);
        return $url;
    }
}

if(!function_exists('invoiceGenerator')) {
    function invoiceGenerator()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString = '';
        for ($i = 0; $i < 8; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        // Membuat invoice dengan format INV-{timestamp}-{karakter-acak}
        $invoice = 'INV-' . time() . '-' . $randomString;
        return $invoice;
    }
}

if(!function_exists('virtual_gateway')) {
    function virtual_gateway($data)
    {
        $merchantCode = app_info('duitku_merchant');
        $apiKey = app_info('duitku_client');

        $paymentAmount = $data['amount'];
        $merchantOrderId = $data['invoice'];
        $productDetails = 'Pembayaran Party Planner mengggunakan Duitku';
        $email = user()->email;
        $phoneNumber = '';
        $additionalParam = '';
        $merchantUserInfo = '';
        $customerVaName = user()->name;
        $callbackUrl = site_url('user', 'orders/callback');
        $returnUrl = site_url('user', 'orders/return');
        $expiryPeriod = 1440;
        $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $apiKey);

        // Customer Detail
        $firstName = 'PARTYPLANNER';
        $lastName = 'Auto Payment';
        $alamat = "Jl. Patimura";
        $city = "Jember";
        $postalCode = "62192";
        $countryCode = "ID";

        $address = array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'address' => $alamat,
            'city' => $city,
            'postalCode' => $postalCode,
            'phone' => $phoneNumber,
            'countryCode' => $countryCode
        );

        $customerDetail = array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'billingAddress' => $address,
            'shippingAddress' => $address
        );

        $item1 = array(
            'name' => 'Pembayaran Party Planner',
            'price' => $data['amount'],
            'quantity' => 1
        );

        $itemDetails = array($item1);
        $params = array(
            'merchantCode' => $merchantCode,
            'paymentAmount' => $paymentAmount,
            'paymentMethod' => $data['method'],
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => $productDetails,
            'additionalParam' => $additionalParam,
            'merchantUserInfo' => $merchantUserInfo,
            'customerVaName' => $customerVaName,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            // 'accountLink' => $accountLink,
            'itemDetails' => $itemDetails,
            'customerDetail' => $customerDetail,
            'callbackUrl' => $callbackUrl,
            'returnUrl' => $returnUrl,
            'signature' => $signature,
            'expiryPeriod' => $expiryPeriod
        );

        $params_string = json_encode($params);

        if(app_info('duitku_sandbox') == 1) {
            $url = 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry'; // Sandbox
        } else {
            $url = 'https://passport.duitku.com/webapi/api/merchant/v2/inquiry'; // Production
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($params_string))
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        //execute post
        $request = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpCode == 200) {
            $result = json_decode($request, true);
            return [
                'url' => $result['paymentUrl'],
                'status' => 'success'
            ];
        } else {
            $result = json_decode($request, true);
            return [
                'msg' => 'Sepertinya terjadi error pada channel pembayaran yang anda pilih',
                'status' => 'error',
                'error' => $result
            ];
        }
    }
}

if(!function_exists('isUrlSecure')) {
    function isUrlSecure($url)
    {
        return strpos($url, 'https://') !== false;
    }

}

if(!function_exists('filterExists')) {
    function filterExists()
    {
        if(!empty(\Illuminate\Support\Facades\Request::input('filterType'))
        || !empty(\Illuminate\Support\Facades\Request::input('categoryFilter'))
        || !empty(\Illuminate\Support\Facades\Request::input('minimumPrice'))
        || !empty(\Illuminate\Support\Facades\Request::input('maximumPrice'))
        || !empty(\Illuminate\Support\Facades\Request::input('searchFilter'))) {
            return true;
        } else {
            return false;
        }
    }
}

if(!function_exists('date_formatting')) {
    function date_formatting($date, $format)
    {
        switch($format)
        {
            case 'd-m-Y':
                return date('d-m-Y', strtotime($date));
            break;

            case 'indonesia':
                $month = array (
                    1 =>   'Januari',
                    'Februari',
                    'Maret',
                    'April',
                    'Mei',
                    'Juni',
                    'Juli',
                    'Agustus',
                    'September',
                    'Oktober',
                    'November',
                    'Desember'
                );
                $reformat = explode('-', $date);
                return $reformat[2] . ' ' . $month[ (int)$reformat[1] ] . ' ' . $reformat[0];
            break;

            case 'timeago':
                $time_difference = time() - strtotime($date);
                if( $time_difference < 1 ) { return '1 detik yang lalu'; }
                $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
                    30 * 24 * 60 * 60       =>  'bulan',
                    24 * 60 * 60            =>  'hari',
                    60 * 60                 =>  'jam',
                    60                      =>  'menit',
                    1                       =>  'detik'
                );

                foreach( $condition as $secs => $str )
                {
                    $d = $time_difference / $secs;
                    if( $d >= 1 )
                    {
                        $t = round( $d );
                        return $t . ' ' . $str . ( $t > 1 ? '' : '' ) . ' yang lalu';
                    }
                }
            break;
        }
    }
}

if(!function_exists('findUser')) {
    function findUser($id)
    {
        return \App\Models\User::find($id)->first();
    }
}

if(!function_exists('switch_page')) {
    function switch_page()
    {

    }
}

if(!function_exists('isSeller')) {
    function isSeller()
    {
        $checkUserModel = \App\Models\Seller::where('user_id', \Illuminate\Support\Facades\Auth::user()->id)->count();
        if($checkUserModel > 0){
            return true;
        } else {
            return false;
        }
    }
}

if(!function_exists('active_page')) {
    function active_page($activePage)
    {
        $currentRoute = \Illuminate\Support\Facades\Route::currentRouteName();
        if ($currentRoute) {
            $routeUri = htmlspecialchars($currentRoute);
            if($routeUri === $activePage) { return 'active'; }
        }
        return '';
    }
}

if(!function_exists('routesAll')) {
    function routesAll()
    {
        $routesInAppPrefix = [];
        foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
            $uri = $route->uri();

            // Check if the route URI starts with the desired prefix
            if (strpos($uri, 'app/') === 0) {
                $routesInAppPrefix[] = $route->action['as'];
            }
        }

        $convertIntoJson = json_encode($routesInAppPrefix, true);
        return json_decode($convertIntoJson, true);
    }
}

/**
 * Retrieves the currently authenticated user.
 *
 * @return User The currently authenticated user.
 */
if (!function_exists('user')) {
    function user()
    {
        return \Illuminate\Support\Facades\Auth::user();
    }
}

if(!function_exists('segment')) {
    /**
     * Retrieves a segment from the current request.
     *
     * @param int $key The key of the segment to retrieve.
     * @return mixed The value of the segment.
     */
    function segment($key)
    {
        return request()->segment($key);
    }
}

if(!function_exists('enum')) {
    /**
     * Retrieves the value of a constant from the GlobalEnum class.
     *
     * @param string $args The name of the constant to retrieve.
     * @return mixed The value of the constant.
     */
    function enum($args)
    {
        return constant("\App\Enums\GlobalEnum::$args");
    }
}

/**
 * Generate a Gravatar URL for a given email.
 *
 * @param string $email The email address to generate the Gravatar URL for.
 * @throws None
 * @return string The Gravatar URL.
 */
if (!function_exists('gravatar_team')) {
    function gravatar_team($email)
    {
        $username = md5($email);
        return "https://www.gravatar.com/avatar/$username?s=70&d=retro&r=y";
    }
}

/**
 * Convert a number into Indonesian Rupiah format.
 *
 * @param int $nominal The number to be converted.
 * @return string The converted number in Indonesian Rupiah format.
 */
if (!function_exists('rupiah')) {
    function rupiah($nominal)
    {
        return 'Rp. ' . number_format($nominal,0,',','.');
    }
}

/**
 * A function that changes the given nominal value into a different format.
 *
 * @param string $nominal The nominal value to be changed.
 * @return string The changed nominal value.
 */
if (!function_exists('rupiah_changer')) {
    function rupiah_changer($nominal)
    {
        if(strlen($nominal) == 4) {
            return substr($nominal, 0, 1) . 'RB';
        } elseif(strlen($nominal) == 5) {
            return substr($nominal, 0, 2) . 'RB';
        } elseif(strlen($nominal) == 6) {
            return substr($nominal, 0, 3) . 'RB';
        } elseif(strlen($nominal) == 7) {
            return substr($nominal, 0, 1) . 'JT';
        } elseif(strlen($nominal) == 8) {
            return substr($nominal, 0, 2) . 'JT';
        } elseif(strlen($nominal) == 9) {
            return substr($nominal, 0, 3) . 'JT';
        } elseif(strlen($nominal) == 10) {
            return substr($nominal, 0, 1) . 'M';
        } elseif(strlen($nominal) == 11) {
            return substr($nominal, 0, 2) . 'M';
        } elseif(strlen($nominal) == 12) {
            return substr($nominal, 0, 3) . 'M';
        } elseif(strlen($nominal) == 13) {
            return substr($nominal, 0, 1) . 'T';
        } elseif(strlen($nominal) == 14) {
            return substr($nominal, 0, 2) . 'T';
        } elseif(strlen($nominal) == 15) {
            return substr($nominal, 0, 3) . 'T';
        } else {
            return 0;
        }
    }
}
/**
 * Formats a phone number.
 *
 * @param string $value The phone number to be formatted.
 * @return string The formatted phone number.
 */
if (!function_exists('phone_formatter')) {
    function phone_formatter($value) {
        // kadang ada penulisan no hp 0811 239 345
        $nohp = str_replace(" ","",$value);
        // kadang ada penulisan no hp (0274) 778787
        $nohp = str_replace("(","",$value);
        // kadang ada penulisan no hp (0274) 778787
        $nohp = str_replace(")","",$value);
        // kadang ada penulisan no hp 0811.239.345
        $nohp = str_replace(".","",$value);

        // cek apakah no hp mengandung karakter + dan 0-9
        if(!preg_match('/[^+0-9]/',trim($nohp))){
        // cek apakah no hp karakter 1-3 adalah +62
            if(substr(trim($nohp), 0, 3)=='+62'){
            $hp = trim($nohp);
            }
            // cek apakah no hp karakter 1 adalah 0
            elseif(substr(trim($nohp), 0, 1)=='0'){
            $hp = '+62'.substr(trim($nohp), 1);
            }
        }
        return $hp;
    }
}
