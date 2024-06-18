<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\DomCrawler\Crawler;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('ba', function() {
    $html = file_get_html('https://database.gdriveplayer.io/movie.php?s=aquaman');
    $table = $html->find('table', 0);
    // $table->removeChild($table->find('tr', 0));
    foreach($table->find('tr') as $items) {
        echo $items->outertext;
    }
});