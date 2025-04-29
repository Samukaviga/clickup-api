<?php

use App\Http\Controllers\ClickUpController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/clickup/workspaces', [ClickUpController::class, 'getWorkspaces']);
Route::get('/clickup/create-task', [ClickUpController::class, 'createTask']);

Route::get('/clickup/spaces', [ClickUpController::class, 'getSpaces']);

Route::get('/clickup/folder', [ClickUpController::class, 'getFolders']);

Route::get('/clickup/list', [ClickUpController::class, 'getLists']);

Route::get('/clickup/tasks', [ClickUpController::class, 'getTasks']);

Route::get('/clickup/task', [ClickUpController::class, 'getTask']);

Route::get('/clickup/members', [ClickUpController::class, 'getMembers']);


Route::get('/teste', [ClickUpController::class, 'teste']);

Route::get('/objeto', function () {

    /*$obj = new \stdClass();
    $obj->nome = "João";

    return [$obj]; */

    return response()->json([
        ['type' => 'text', 'value' => 'Bem-vindo!'],
    ]);

});


Route::get('/envia', function () {
    $response = Http::post('https://webhook.sellflux.app/webhook/custom/lead/e0bf142727516605c885676cc9e8a9c9?name=nome&email=email&phone=telefone', [
        'nome' => 'Samuel GOmes',
        'email' => 'samuelgomes2021@gmail.com',
        'telefone' => '1196512-4506',
        'hora' => '12:00',
    ]);

    return $response->body(); // Retorna a resposta para ver o que o Sellflux enviou de volta
}); 

/*
Route::get('/envia', function () {
    $dados = [
        'nome' => 'xibinha',
        'email' => 'xinbinha@gmail.com',
        'telefone' => '11999999',
        'hora_agendamento' => '12:00'  // Alterado para snake_case
    ];

    $resposta = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ])->post('https://webhook.sellflux.app/webhook/custom/lead/e0bf142727516605c885676cc9e8a9c9', [
        'name' => $dados['nome'],
        'email' => $dados['email'],
        'phone' => $dados['telefone'],
        'hora_agendamento' => $dados['hora_agendamento']
    ]);

    if ($resposta->successful()) {
        return response()->json(['sucesso' => true, 'dados' => $resposta->json()]);
    }

    return response()->json([
        'erro' => 'Falha na requisição',
        'detalhes' => $resposta->body(),
        'status' => $resposta->status()
    ], 500);
}); */





require __DIR__.'/auth.php';
