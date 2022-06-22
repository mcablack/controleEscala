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

/** PAGINA INICIAL */
Route::get('/', function () {
    return view('welcome');
});
/** FIM DA PAGINA INICIAL */    

// Route:: resource('/posts','PostController');



/** INICIO OPERADOR */
Route::prefix('/operador')->group(function(){

    Route::get('/meuteste', 'OperadorController@teste');
    Route::any('/operadorescala/{matricula?}', 'OperadorController@escala');
    Route::any('/trocacasada/{matricula?}', 'OperadorController@trocaCasada');
    Route::any('/get/escala/troca/{date?}/{p_operador?}', 'OperadorController@escalaTroca');
    Route::post('/get/solicitacoes/troca','OperadorController@filtrarSolicitacoes');
    Route::any('/solicitar/troca/casada', 'OperadorController@solicitarTrocaCasada');
    Route::get('/minhassolicitacoes', 'OperadorController@solicitacoesDeTroca');
    Route::post('/aceitar/troca/casada', 'OperadorController@aceitarTrocaCasada');
    
});
/** FIM OPERADOR */


/** INICIO DO SUPERVISOR */
Route::prefix('/surpevisor')->group(function(){
    Route::any('/supervisorequipe/{matricula?}', 'SupervisorController@listarEquipe');
    Route::any('/trocaPendente/{matricula?}', 'SupervisorController@listarTrocas');
    Route::any('/trocaEspecial/{matricula?}', 'SupervisorController@trocaEspecial');
    Route::any('/solicitacoesTodas', 'SupervisorController@listarSolicitacoesTodas');
    Route::post('/acaoSolicitacao','SupervisorController@acaoSolicitacao');
    Route::get('/get/solicitacao/relacionamento/{id_relacionamento?}/{id_solicitacao?}','SupervisorController@getSolicitacaoRelacionamento');
});
/** FIM DO SUPERVISOR */


/** INICIO DO COORDENADOR - TRAFEGO */
Route::prefix('/coordenador')->group(function(){
    Route::any('/operadorestrafego/{matricula?}', 'CoordenadorController@listarFuncionarios');
    Route::get('/coordenadorestrafego', 'CoordenadorController@listarFuncionariosCoord');
    Route::get('/solicitacoes/trocaCasada', 'CoordenadorController@solicitacoesTrocaCasada');
    Route::get('/get/solicitacao/relacionamento/{id_relacionamento?}/{id_solicitacao?}','CoordenadorController@getSolicitacaoRelacionamento');
});
/** FIM DO COORDENADOR - TRAFEGO */


/** INICIO PLANEJAMENTO */
Route::prefix('/trafego')->group(function(){

    Route::prefix('/colaboradores')->group(function(){
        Route::get('/', 'PlanejamentoController@listarColaboradores');
        Route::get('/editar/{matricula?}', 'PlanejamentoController@editarColaboradores');
        Route::post('/editar/{matricula?}', 'PlanejamentoController@updateColaboradores');
    });
    Route::any('/solicitacoesTodas', 'SupervisorController@listarTrocas');
    Route::get('/get/solicitacao/relacionamento/{id_relacionamento?}/{id_solicitacao?}','SupervisorController@getSolicitacaoRelacionamento');
    Route::get('/trocaPendenteTrafego', 'PlanejamentoController@listarTrocasTrafego');
    Route::get('/trocasTodas', 'PlanejamentoController@listarSolicitacoes');
    Route::get('/importacaoescala', 'PlanejamentoController@importacaoEscala');
    Route::post('/importacaoescala', 'PlanejamentoController@importacaoEscalaAction')->name('importacao');
});
/** FIM PLANEJAMENTO */