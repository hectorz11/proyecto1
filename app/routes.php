<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::get('/',function(){
	return View::make('form');
});

Route::post('/', function()
{
	// primero vamos a definir las reglas de validatcion del formulario
	$rules = array('link' => 'required|url');

	// entonces corremos el formulario de validacion
	$validation = Validator::make(Input::all(),$rules);

	// si la validacion falla, volvemos a la pagina principal con un 
	// mensaje de error
	if($validation->fails()){
		return Redirect::to('/')->withInput()->withErrors($validation);
	}else{
		// ahora vamos a comprobar si ya tenemos el enlace en
		// nuestra base de datos, si es asi ya tenemos nuestro
		// primer resultado.
		$link = Link::where('url','=',Input::get('link'))->first();
		// si tenemos la URL guardada en nuestra base de datos,
		// nos proporciona esa informacion a visualizar.
		if($link){
			return Redirect::to('/')->withInput()->with('link',$link->hash);
		// en caso contrario creamos una nueva direccion URL unica
		}else{
			// primero creamos un nuevo hash unico
			do{
				$newHash = Str::random(6);
			}while(Link::where('hash','=',$newHash)->count() > 0);
			// ahora vamos a crear un nuevo registro en la base de datos
			Link::create(array(
				'url' => Input::get('link'),
				'hash' => $newHash
			));
			// y entonces volvemos la nueva informacion URL acortada a 
			// nuestra accion
			return Redirect::to('/')->withInput()->with('link',$newHash);
		}
	}
});

Route::get('{hash}',function($hash) {
	//First we check if the hash is from a URL from ourdatabase
	$link = Link::where('hash','=',$hash)->first();
	//If found, we redirect to the URL
	if($link) {
		return Redirect::to($link->url);
	//If not found, we redirect to index page with errormessage
	} else {
		return Redirect::to('/')->with('message','Invalid Link');
	}
})->where('hash', '[0-9a-zA-Z]{6}');