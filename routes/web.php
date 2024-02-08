<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PayPalController;
use Illuminate\Support\Facades\Config;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('paypal', [PayPalController::class, 'index'])->name('paypal');
Route::get('paypal/payment', [PayPalController::class, 'payment'])->name('paypal.payment');
Route::get('paypal/payment/success', [PayPalController::class, 'paymentSuccess'])->name('paypal.payment.success');
Route::get('paypal/payment/cancel', [PayPalController::class, 'paymentCancel'])->name('paypal.payment/cancel');



/*
 * admins   ------------- Developer -------------
 * users    -------------- Shops ----------------
 * employees ------------- Employees ------------
 */





/*
 * Create new user in database and migrate all files for this user
 */
Route::get('create-new-user', function (){


    DB::beginTransaction();

    try {

        $user = 'user_'.rand(1,1000).uniqid();

        $query = "CREATE DATABASE IF NOT EXISTS $user";

        DB::statement($query);

        DB::purge('mysql');
        Config::set('database.connections.mysql.database',$user);
        DB::reconnect('mysql');

        Artisan::call('migrate --path=database/migrations/clients --seed');

        User::create([
            'name' => 'shop_3',
            'email' => 'email3@gmail.com',
            'password' => Hash::make('123456'),
        ]);

        DB::purge('mysql');
        Config::set('database.connections.mysql.database','clients');
        DB::reconnect('mysql');


        User::create([
            'shop_name' => 'shop_3',
            'email' => 'email3@gmail.com',
            'password' => Hash::make('123456'),
            'database' => $user,
        ]);

        DB::commit();

        return 'Database created successfully!';

    }catch (\Exception $exception){

        DB::rollBack();
        return 'User Not Created '. $exception->getMessage();

    }

});



Route::group(['prefix' => 'user'], function (){


//    Config::set('database.connections.mysql.database','user_77265c36bb00735e');

    Route::get('all-categories', function (){

//        $routePrefix = request()->route()->getPrefix();

         //user/all-categories

        $string = request()->route()->uri();

        // Split the string into an array using the forward slash (/) as the delimiter
        $list = explode("/", $string);

        dd($list);

        $routePrefix = str_replace('/','',$routePrefix);
        dd($routePrefix);

        dd(\App\Models\Category::query()->get());

    });

});


/*
 * Authentication
 * Website
 */
