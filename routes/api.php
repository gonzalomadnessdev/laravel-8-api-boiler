<?php

use Dingo\Api\Routing\Router;

/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- | | Here is where you can register API routes for your application. These | routes are loaded by the RouteServiceProvider within a group which | is assigned the "api" middleware group. Enjoy building your API! | */

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {

    /*Prefix 'auth'*/
    $api->group(['prefix' => 'auth'], function (Router $api) {
        $api->post('login', 'App\Api\V1\Controllers\AuthController@login');
        // $api->post('register', 'App\Api\V1\Controllers\AuthController@register');
        $api->group(['middleware' => ['jwt.auth']], function (Router $api) {
            $api->post('logout', 'App\Api\V1\Controllers\AuthController@logout');
            $api->post('refresh', 'App\Api\V1\Controllers\AuthController@refresh');
            $api->get('me', 'App\Api\V1\Controllers\AuthController@me');
            $api->post('cambiar-contrasena', 'App\Api\V1\Controllers\AuthController@cambiarContrasena');
        });
    });

    $api->group(['middleware' => ['jwt.auth']], function (Router $api) {

        /*Usuarios*/
        $api->group(['middleware' => ['tienePermiso:usuarios.ver']], function (Router $api) {
            $api->get('usuarios', 'App\Api\V1\Controllers\UserController@mostrarUsuarios');
            $api->get('usuarios/{id}', 'App\Api\V1\Controllers\UserController@mostrarUsuario')->where('id', '[0-9]+');
        });

        $api->group(['middleware' => ['tienePermiso:usuarios.crear']], function (Router $api) {
            $api->post('usuarios', 'App\Api\V1\Controllers\UserController@crearUsuario');
        });

        $api->group(['middleware' => ['tienePermiso:usuarios.editar']], function (Router $api) {
            $api->put('usuarios/{id}', 'App\Api\V1\Controllers\UserController@modificarUsuario')->where('id', '[0-9]+');
            $api->post('usuarios/cambiar-contrasena', 'App\Api\V1\Controllers\UserController@cambiarContrasena');
        });

        $api->group(['middleware' => ['tienePermiso:usuarios.eliminar']], function (Router $api) {
            $api->delete('usuarios/{id}', 'App\Api\V1\Controllers\UserController@borrarUsuario')->where('id', '[0-9]+');
        });

        /*Roles*/
        $api->group(['middleware' => ['tienePermiso:roles.ver']], function (Router $api) {
            $api->get('roles', 'App\Api\V1\Controllers\RolesYPermisosController@mostrarRoles');
            $api->get('roles/{id}', 'App\Api\V1\Controllers\RolesYPermisosController@mostrarRol')->where('id', '[0-9]+');
            $api->get('permisos', 'App\Api\V1\Controllers\RolesYPermisosController@mostrarPermisos');
        });

        $api->group(['middleware' => ['tienePermiso:roles.crear']], function (Router $api) {
            $api->post('roles', 'App\Api\V1\Controllers\RolesYPermisosController@crearRol');
        });

        $api->group(['middleware' => ['tienePermiso:roles.editar']], function (Router $api) {
            $api->put('roles/{id}', 'App\Api\V1\Controllers\RolesYPermisosController@modificarRol')->where('id', '[0-9]+');
        });

        $api->group(['middleware' => ['tienePermiso:roles.eliminar']], function (Router $api) {
            $api->delete('roles/{id}', 'App\Api\V1\Controllers\RolesYPermisosController@borrarRol')->where('id', '[0-9]+');
        });





;


    });
});
