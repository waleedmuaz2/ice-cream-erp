<?php

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

Route::get('/',function(){
	return redirect()->route('login');
});

Auth::routes();

Route::group(['prefix' => 'user' , 'middleware' => 'auth'], function () {
    
    Route::get('recover', 'InvoiceController@updation')->name('recover_data');
    
    Route::get('/print-invoice/{id}','InvoiceController@printInvoice')->name('print.invoice');
    
	Route::get('/dashboard','HomeController@userDashboard')->name('admin.home');
    
    Route::group(['middleware' => 'admin'] , function(){
        
        //Notifications
        Route::get('notifySeen' , function(){
            $user = App\User::find(Auth::id());
            $user->is_notified = 0;
            $user->save();
            return "true";
        })->name('notify.seen');
        Route::get('notifyClicked/{id?}' , function($id){
            App\Notification::where('id' , $id)->delete();
        })->name('notify.clicked');
        
        Route::get('/clearAll','UserController@clearAll')->name('clear.all');
        
        Route::get('/sellTotalClear','UserController@sellTotalClear')->name('admin.sell.clear');
        
        //Category
        Route::get('/allCategories','CategoryController@index')->name('all.categories');
        
        Route::post('/storeCategory','CategoryController@storeCategory')->name('save.category');
        
        Route::post('/updateCategory/{id}','CategoryController@updateCategory')->name('update.category');
        
        Route::get('/getCategory/{id}','CategoryController@getCategory');
        
        Route::get('/deleteCategory/{id}','CategoryController@deleteCategory')->name('delete.category');
        
        //Product
        Route::get('/allProducts/{customer_id?}','ProductController@index')->name('all.products');
        
        Route::post('/storeProduct','ProductController@storeProduct')->name('save.product');
        
        Route::post('/updateProduct/{id}','ProductController@updateProduct')->name('update.product');

        Route::get('/addProduct', 'ProductController@addProduct')->name('add.product');
        
        Route::get('/getProduct/{id}','ProductController@getProduct')->name('edit.product');
        
        Route::get('/deleteProduct/{id}','ProductController@deleteProduct')->name('delete.product');

            //Seller , Users
        Route::group(['middleware' => 'super'] , function(){
            Route::get('/allUsers','SellerController@getUsers')->name('all.users');

            // Route::get('/getSubAdmins','SellerController@getUsers')->name('all.users');

            Route::get('/subAdminSellers/{id}','SellerController@indexAll')->name('subadmin.sellers');

            Route::get('/subAdminCustomers/{id}','CustomerController@indexAll')->name('subadmin.customers');
            
            Route::get('/deleteSubAdmin/{id}','SubAdminController@deleteSubAdmin')->name('delete.subadmin');

            Route::get('/subAdminInvoices/{id}','InvoiceController@indexAll')->name('subadmin.invoices');
        });

        Route::get('/allSellers','SellerController@index')->name('all.sellers');

  
        

        Route::get('/addSeller','SellerController@addSeller')->name('add.user');
        
        Route::post('/storeSeller/{update?}','SellerController@storeSeller')->name('create.user');
        
        Route::post('/updateSeller/{id}','SellerController@updateSeller')->name('update.seller');
        
        Route::get('/getSeller/{id}','SellerController@getSeller')->name('edit.seller');

        Route::get('/getSellerSells/{id}','SellerController@getSellerSells')->name('view.seller.sells');
        
        Route::get('/deleteSeller/{id}','SellerController@deleteSeller')->name('delete.seller');
        
        Route::get('/ChangeUserStatus/{id}/{status}','SellerController@ChangeUserStatus')->name('unblock.admin');



        Route::get('/deleteInvoice/{id}','InvoiceController@deleteInvoice')->name('delete.invoice');
        
        Route::get('/approveInvoice/{id?}','InvoiceController@approveInvoice')->name('approve.invoice');
        
        Route::post('/approveInvoiceMult','InvoiceController@approveInvoiceMult')->name('approve.invoice.multiple');

        Route::post('validatePin' , 'UserController@validatePin')->name('validate.pin');

        //Pay Amount
        Route::get('/paidHistory' , 'UserController@paidHistory')->name('paid.history');

        Route::post('/payAmount' , 'UserController@payAmount')->name('pay.amount');    

        Route::get('/sellRecord' , 'UserController@sellRecord')->name('sell.record');
    });
    
    Route::group(['prefix' => 'area'], function(){
        Route::get('add', 'AreaController@add')->name('add.area');
        Route::get('list', 'AreaController@list')->name('list.area');
        Route::get('edit/{id}', 'AreaController@edit')->name('edit.area');
        Route::post('save/{id?}', 'AreaController@save')->name('save.area');
        Route::get('delete/{id}', 'AreaController@delete')->name('delete.area');
    });
    
    //Customer
    Route::get('/allCustomers','CustomerController@index')->name('all.customers');

    Route::get('/addCustomer', 'CustomerController@addCustomer')->name('add.customer');
    
    Route::get('/customerInvoices/{customer_id?}' , 'InvoiceController@customerInvoices')->name('customer.invoices');
    
    Route::get('/myCustomers/{user_id}','CustomerController@myCustomers');
    
    Route::post('/storeCustomer','CustomerController@storeCustomer')->name('create.customer');
    
    Route::post('/updateCustomer/{id}','CustomerController@updateCustomer')->name('update.customer');
    
    Route::get('/editCustomer/{id}','CustomerController@getCustomer')->name('edit.customer');
    
    Route::get('/deleteCustomer/{id}','CustomerController@deleteCustomer')->name('delete.customer');

    Route::get('/checkCustomPrice/{id?}','CustomerController@checkCustomPrice')->name('check.custom.price');  
    
    //Order Taker
    Route::get('/all_ot','OTController@index')->name('all.ot');
    Route::get('/editOT/{id}','OTController@getOT')->name('edit.ot');
    Route::post('/updateOT/{id}','OTController@updateOT')->name('update.ot');
    Route::get('/deleteOT/{id}','OTController@deleteOT')->name('delete.ot');
    Route::get('/ChangeOTStatus/{id}/{status}','OTController@ChangeUserStatus')->name('unblock.ot');
    Route::post('/payOTAmount/{id}' , 'OTController@payAmount')->name('pay.ot.amount'); 
    Route::get('/OtPaidHistory/{id}' , 'OTController@paidHistory')->name('ot.paid.history');

    //Orders
    Route::group(['prefix' => 'order'] , function(){
        Route::get('/orderDetails/{id?}','OrderController@getorderDetail')->name('order.detail');
        Route::get('/createOrder','OrderController@createOrder')->name('create.order'); 
        Route::post('/storeOrder','OrderController@storeOrder')->name('store.order');
        Route::match(array('GET','POST'),'/allOrders','OrderController@getAllOrders')->name('all.orders');
        Route::match(array('GET','POST'),'/importantOrders','OrderController@getImportantOrders')->name('important.orders');
        Route::match(array('GET','POST'),'/unconfirmedOrders','OrderController@getUnconfirmedOrders')->name('unconfirmed.orders');
        Route::match(array('GET','POST'),'/sellerConfirmedOrders','OrderController@getSellerConfirmedOrders')->name('confirmed.orders.seller');
        Route::match(array('GET','POST'),'/adminConfirmedOrders','OrderController@getAdminConfirmedOrders')->name('confirmed.orders.admin');
        Route::post('/confirmOrderMult','OrderController@confirmOrderMult')->name('approve.order.multiple');
        Route::post('/searchByDate/{check?}','OrderController@dateFilter')->name('order.date.filter');
       
        Route::get('/geOrder/{id}','OrderController@getOrder')->name('edit.order');
       
        Route::post('/updateOrder/{id}','OrderController@updateOrder')->name('update.order');
        Route::get('/deleteOrder/{id}','OrderController@deleteOrder')->name('delete.order');
    });
    //Invoices
    Route::group(['prefix' => 'invoice'] , function(){
    	Route::get('/newInvoice','InvoiceController@newInvoice')->name('add.invoice');
    
	    Route::get('/allInvoices/{get_unapproved?}','InvoiceController@index')->name('invoices');

	    Route::get('/invoiceDetails/{id?}','InvoiceController@getinvoiceDetail')->name('invoice.detail');
	    
	    Route::post('/storeInvoice','InvoiceController@storeInvoice')->name('store.invoice');
	    
	    Route::post('/updateInvoice/{id}','InvoiceController@updateInvoice')->name('update.invoice');
	    
	    Route::get('/getInvoice/{id}','InvoiceController@getInvoice')->name('edit.invoice');
	    
	    
	    Route::post('/searchByDate/{unapproved?}','InvoiceController@dateFilter')->name('date.filter');
	
    });   
});

Route::group(['prefix'=>'customer','middleware' => 'auth:api'], function () {
     
});
