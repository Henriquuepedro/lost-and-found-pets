<?php

use Illuminate\Support\Facades\Route;

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


/**
 * CLIENTES
 */
Route::get('/', 'User\HomeController@index')->name('user.home');

Route::get('/entrar', 'User\LoginController@login')->name('user.login');
Route::post('/entrar', 'User\LoginController@loginPost')->name('user.login.post');
Route::get('/sair', 'User\LoginController@logout')->name('user.logout');

Route::get('/esqueceu-senha', 'User\ForgotPasswordController@forgotPassword')->name('user.forgotPassword');
Route::post('/esqueceu-senha', 'User\ForgotPasswordController@forgotPasswordPost')->name('user.forgotPassword.post');

Route::get('/redefinir-senha/{hash}', 'User\ForgotPasswordController@resetPassword')->name('user.resetPassword');
Route::post('/redefinir-senha', 'User\ForgotPasswordController@resetPasswordPost')->name('user.resetPassword.post');

Route::get('/cadastro', 'User\RegisterController@register')->name('user.register');
Route::post('/cadastro', 'User\RegisterController@registerPost')->name('user.register.post');

Route::get('/carrinho', 'User\CartController@cart')->name('user.cart');

Route::get('/produtos/{id}', 'User\ProductController@product')->name('user.product');
Route::get('/produtos', 'User\ProductController@products')->name('user.products');

Route::get('/sobre', 'User\AboutController@about')->name('user.about');
Route::get('/contato', 'User\AboutController@contact')->name('user.contact');
Route::post('/contact', 'User\ContactController@contact')->name('user.mail.contact');
Route::get('/depoimentos', 'User\AboutController@testimonies')->name('user.testimonies');

Route::get('/politica-reembolso', 'User\PolicyController@refund')->name('user.policy.refund');
Route::get('/seguranca-privacidade', 'User\PolicyController@security')->name('user.policy.security');
Route::get('/fretes-entregas', 'User\PolicyController@freight')->name('user.policy.freight');

/** ROTAS AUTENTICADO */
Route::group(['middleware' => 'auth:client'], function (){
    Route::get('/minhaconta/cadastro', 'User\AccountController@edit')->name('user.account.edit');
    Route::post('/minhaconta/cadastro', 'User\AccountController@editPost')->name('user.account.edit.post');

    Route::get('/minhaconta/enderecos', 'User\AddressController@address')->name('user.account.address');
    Route::post('/minhaconta/enderecos', 'User\AddressController@addressPost')->name('user.account.address.post');
    Route::post('/minhaconta/enderecos/edit', 'User\AddressController@addressEdit')->name('user.account.address.edit');
    Route::post('/minhaconta/enderecos/delete', 'User\AddressController@addressDelete')->name('user.account.address.delete');
    Route::post('/minhaconta/enderecos/update_default', 'User\AddressController@addressDefault')->name('user.account.address.default');

    Route::get('/minhaconta/pedidos/finalizar', 'User\OrderController@checkout')->name('user.order.checkout');
    Route::post('/minhaconta/pedidos/finalizar/enviar', 'User\OrderController@checkoutSend')->name('user.order.checkout.send');
    Route::get('/minhaconta/pedidos/finalizar/{id}', 'User\OrderController@orderCreated')->name('user.order.checkout.created');

//    Route::get('/minhaconta', 'User\AccountController@index')->name('user.account');
    Route::get('/minhaconta', 'User\AccountController@edit')->name('user.account');
    Route::get('/minhaconta/pedidos', 'User\AccountController@orders')->name('user.account.orders');
    Route::post('/minhaconta/pedidos/recebido', 'User\OrderController@received')->name('user.orders.received');
    Route::get('/minhaconta/pedidos/{id}', 'User\OrderController@order')->name('user.account.order');

    Route::post('/minhaconta/pedidos/avaliar', 'User\RateController@rate')->name('user.rates.rate');

    Route::post('/minhaconta/depoimento', 'User\RateController@newForUserTestimony')->name('user.account.testimony');

    //AJAX COM AUTH
    Route::post('/queries/ajax/searchAdrress', 'User\OrderController@searchAdrress')->name('queries.ajax.searchAdrress');
    Route::post('/queries/ajax/insertAdrress', 'User\AddressController@insertAjax')->name('queries.ajax.insertAdrress');
    Route::post('/queries/ajax/updateAdrress', 'User\AddressController@updateAjax')->name('queries.ajax.updateAdrress');
    Route::post('/queries/ajax/getAddress', 'User\AddressController@getAddressAjax')->name('queries.ajax.getAddress');
    Route::post('/queries/ajax/getValueFreteUnitario', 'User\OrderController@getValueFreteUnitario')->name('queries.ajax.getValueFreteUnitario');
    Route::post('/queries/ajax/insertCupom', 'User\OrderController@insertCupom')->name('queries.ajax.insertCupom');
    Route::post('/queries/ajax/getValueParcel', 'User\OrderController@getValueParcelsQtyParcel')->name('queries.ajax.getValueParcel');

});

/** ROTAS PARA CONSULTA AJAX SEM NECESSIDADE DE AUTH */
Route::group(['prefix' => '/queries/ajax', 'name' => 'queries.ajax.'], function (){
    Route::post('/addCart', 'User\CartController@add')->name('addCart');
    Route::post('/updateCart', 'User\CartController@update')->name('updateCart');
    Route::post('/deleteCart', 'User\CartController@delete')->name('deleteCart');
    Route::post('/defineCepUser', 'User\CartController@defineCepUser')->name('defineCepUser');
});

/** ROTAS PARA RECEBIMENTO DE NOTIFICAÇÕES DO MERCADO PAGO */
Route::group(['middleware' => '\App\Http\Middleware\VerifyCsrfToken'], function (){
    Route::post('/pagseguro/notification/', 'User\MercadoPagoController@notification')->name('pagseguro_notification');
    Route::post('/mercadopago/notification/', 'User\MercadoPagoController@notification')->name('mercadopago_notification');
});

/**
 * ADMINS
 */
Route::group(['prefix' => 'admin', 'name' => 'admin.'], function (){
    Auth::routes();
});

Route::group(['middleware' => 'auth:admin', 'namespace' => 'Admin', 'prefix' => 'admin', 'name' => 'admin.'], function (){

    Route::get('/home', 'HomeController@dashboard')->name('admin.home');
    Route::get('/', 'HomeController@dashboard')->name('admin.home');
    Route::get('/register', function (){
       return redirect()->route('admin.home');
    });

    Route::get('/perfil', 'ProfileController@index')->name('admin.profile');
    Route::post('/perfil/atualizar', 'ProfileController@update')->name('admin.profile.update');

    /** Produtos */
    Route::group(['prefix' => '/produtos'], function () {
        Route::get('/', 'ProductController@list')->name('admin.products');
        Route::get('/novo', 'ProductController@new')->name('admin.products.new');
        Route::post('/cadastro', 'ProductController@insert')->name('admin.products.insert');

        Route::get('/{id}', 'ProductController@edit')->name('admin.products.edit');
        Route::post('/atualizar', 'ProductController@update')->name('admin.products.update');

        Route::post('/delete', 'ProductController@delete')->name('admin.products.delete');
    });

    /** Pedidos */
    Route::group(['prefix' => '/pedidos'], function () {
        Route::get('/', 'OrderController@list')->name('admin.orders');
        Route::post('/entregue', 'OrderController@received')->name('admin.orders.received');
        Route::post('/cancelar', 'OrderController@cancel')->name('admin.orders.cancel');
        Route::get('/etiquetas', 'OrderController@tags')->name('admin.orders.freight.tags');
        Route::get('/envios/{id}', 'OrderController@freight')->name('admin.orders.freight');
        Route::post('/envios/atualizar', 'OrderController@freightUpdate')->name('admin.orders.freight.update');
        Route::get('/envios/etiqueta/{id}', 'OrderController@generateTag')->name('admin.orders.freight.generateTag');
        Route::get('/{id}', 'OrderController@view')->name('admin.orders.view');
    });

    /** Clientes */
    Route::group(['prefix' => '/clientes'], function () {
        Route::get('/', 'ClientController@list')->name('admin.clients');
        Route::get('/{id}', 'ClientController@view')->name('admin.client.view');
    });

    /** Cupom */
    Route::group(['prefix' => '/cupons'], function () {
        Route::get('/', 'CouponController@list')->name('admin.coupons');
        Route::get('/novo', 'CouponController@new')->name('admin.coupon.new');
        Route::post('/atualizar', 'CouponController@update')->name('admin.coupon.update');
        Route::post('/cadastrar', 'CouponController@insert')->name('admin.coupon.insert');
        Route::post('/excluir', 'CouponController@remove')->name('admin.coupon.delete');
        Route::get('/{id}', 'CouponController@edit')->name('admin.coupon.edit');
    });

    /** Depoimento */
    Route::group(['prefix' => '/depoimentos'], function () {
        Route::get('/', 'TestimonyController@list')->name('admin.testimonies');
        Route::get('/novo', 'TestimonyController@new')->name('admin.testimonies.new');
        Route::post('/excluir', 'TestimonyController@remove')->name('admin.testimonies.delete');
        Route::post('/atualizar', 'TestimonyController@update')->name('admin.testimonies.update');
        Route::post('/cadastrar', 'TestimonyController@insert')->name('admin.testimonies.insert');
        Route::get('/{id}', 'TestimonyController@edit')->name('admin.testimonies.edit');
    });

    /** Avaliações de Produto */
    Route::group(['prefix' => '/avaliacoes'], function () {
        Route::get('/', 'RateController@list')->name('admin.rate');
        Route::post('/alterar', 'RateController@change')->name('admin.rate.change');
        Route::get('/novo', 'RateController@new')->name('admin.rate.new');
        Route::post('/cadastrar', 'RateController@insert')->name('admin.rate.insert');
        Route::post('/atualizar', 'RateController@update')->name('admin.rate.update');
        Route::post('/excluir', 'RateController@remove')->name('admin.rate.delete');
        Route::get('/{id}', 'RateController@edit')->name('admin.rate.edit');
    });

    /** Layout Usuário */
    Route::group(['prefix' => '/banner'], function () {
        Route::get('/', 'BannerController@list')->name('admin.banners');
        Route::post('/cadastrar', 'BannerController@insert')->name('admin.banners.insert');
        Route::post('/excluir', 'BannerController@remove')->name('admin.banners.delete');
    });

    /** Imagem título página */
    Route::group(['prefix' => '/imagem_titulo'], function () {
        Route::get('/', 'ProfileController@image_title')->name('admin.image_title');
        Route::post('/insert', 'ProfileController@image_title_insert')->name('admin.image_title.insert');
    });

    /** Promoções */
    Route::group(['prefix' => '/promocoes'], function () {
        Route::get('/', 'PromotionsController@list')->name('admin.promotions');
        Route::get('/novo', 'PromotionsController@new')->name('admin.promotion.new');
        Route::post('/insert', 'PromotionsController@insert')->name('admin.promotion.insert');
        Route::get('/{id}', 'PromotionsController@edit')->name('admin.promotions.edit');
        Route::post('/update', 'PromotionsController@update')->name('admin.promotion.update');
        Route::post('/excluir', 'PromotionsController@remove')->name('admin.promotions.delete');
    });

    /** Sobre */
    Route::group(['prefix' => '/sobre'], function () {
        Route::get('/', 'ProfileController@about')->name('admin.about');
        Route::post('/insert', 'ProfileController@about_insert')->name('admin.about.insert');
    });

    /** Relatórios */
    Route::group(['prefix' => '/relatorio'], function () {
        Route::get('/conciliacao-pedidos', 'ReportController@order_reconciliation')->name('admin.reports.order_reconciliation');
    });

    /** Ajax */
    Route::group(['prefix' => '/queries/ajax', 'name' => 'queries.ajax.'], function (){
        Route::get('/salesChart', 'HomeController@salesChart')->name('salesChart');
        Route::get('/salesChartWeek', 'HomeController@salesChartWeek')->name('salesChartWeek');
        Route::get('/pieChartIten', 'HomeController@pieChartIten')->name('pieChartIten');
        Route::get('/pieChartClient', 'HomeController@pieChartClient')->name('pieChartClient');
        Route::get('/mapChartstate', 'HomeController@mapChartState')->name('mapChartstate');
        Route::post('/viewRate', 'RateController@viewRate')->name('viewRate');
        Route::post('/rearrangeOrderBanners', 'BannerController@rearrangeOrder')->name('rearrangeOrder');
        Route::post('/testMailSend', 'ProfileController@testeConnectionSmtp')->name('testMailSend');
        Route::get('/salesChartYear', 'HomeController@salesChartYear')->name('salesChartYear');
    });

    /** Testes */
    Route::group(['prefix' => '/test'], function () {
        Route::get('/notification', '_TestController@testNotification')->name('test.notification');
        Route::get('/google-analytics', '_TestController@gogleAnalytics')->name('test.notification');
    });
});

/** Jobs */
Route::get('/sendMailBillet/{hash}', 'Job\JobController@sendMailBillet');
Route::get('/cancelBillet/{hash}', 'Job\JobController@cancelBillet');
