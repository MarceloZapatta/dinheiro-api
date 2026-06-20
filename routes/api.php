<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganizacoesController;
use App\Http\Controllers\CoresController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\MovimentacaoImportacoesController;
use App\Http\Controllers\DashboardsController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\CreditCardInvoiceController;
use App\Http\Controllers\IntegracaoJunoController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\UfsController;
use App\Http\Controllers\MonthlyReportController;

Route::get('/', function () {
    return 'Poupis API v1.0.0';
});

Route::options('/{any}', function () {
    return response()->noContent();
})->where('any', '.*');


Route::prefix('v2')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('esqueci-senha', [AuthController::class, 'esqueciSenha']);
        Route::post('verificar-recuperar-senha', [AuthController::class, 'verificarRecuperarSenha'])
            ->name('recuperar-senha');
        Route::get('verificar-email', [AuthController::class, 'verificarEmail'])
            ->name('verificar-email');
        Route::post('sair', [AuthController::class, 'sair']);
        Route::post('atualizar', [AuthController::class, 'atualizar']);
        Route::post('perfil', [AuthController::class, 'perfil']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('transactions', TransactionsController::class)->except(['show', 'edit', 'create']);

        Route::prefix('transactions/import')->group(function () {
            Route::post('/', [MovimentacaoImportacoesController::class, 'import']);
            Route::post('/image', [MovimentacaoImportacoesController::class, 'importImage']);
            Route::post('/excel', [MovimentacaoImportacoesController::class, 'importarExcel']);
            Route::post('/codigo-barras', [MovimentacaoImportacoesController::class, 'importarCodigoBarras']);
            Route::post('/ofx', [MovimentacaoImportacoesController::class, 'importOfx']);
            Route::post('/{id}/confirm-all', [MovimentacaoImportacoesController::class, 'confirmAll']);
            Route::post('/{id}/confirm/{transactionId}', [MovimentacaoImportacoesController::class, 'confirmSingle']);
            Route::get('/{id}', [MovimentacaoImportacoesController::class, 'show']);
            Route::get('/', [MovimentacaoImportacoesController::class, 'index']);
        });

        Route::get('reports/monthly', [MonthlyReportController::class, 'generateReport']);

        Route::get('cores', [CoresController::class, 'index']);

        Route::apiResource('/contas', AccountsController::class);
        Route::apiResource('/credit-cards', CreditCardController::class);
        Route::apiResource('/credit-cards/{id}/invoices', CreditCardInvoiceController::class);

        Route::prefix('categorias')->group(function () {
            Route::get('/', [CategoriasController::class, 'index']);
        });

        Route::get('organizacoes', [OrganizacoesController::class, 'index']);
        Route::post('organizacoes/convite', [OrganizacoesController::class, 'aceitarConvite']);
        Route::post('organizacoes', [OrganizacoesController::class, 'store']);

        Route::prefix('categorias')->group(function () {
            Route::post('/', [CategoriasController::class, 'store']);
            Route::get('/{id}', [CategoriasController::class, 'show']);
            Route::put('/{id}', [CategoriasController::class, 'update']);
            Route::delete('/{id}', [CategoriasController::class, 'destroy']);
        });

        Route::prefix('movimentacoes')->group(function () {
            Route::prefix('importacoes')->group(function () {
                Route::get('/', [MovimentacaoImportacoesController::class, 'index']);
                Route::post('/excel', [MovimentacaoImportacoesController::class, 'importarExcel']);
                Route::post('/codigo-barras', [MovimentacaoImportacoesController::class, 'importarCodigoBarras']);
                Route::get('/{id}', [MovimentacaoImportacoesController::class, 'show']);
            });

            Route::prefix('dashboards')->group(function () {
                Route::get('/por-categoria', [DashboardsController::class, 'porCategoria']);
                Route::get('/movimentacoes-anual', [DashboardsController::class, 'movimentacoesAnual']);
            });
        });

        Route::prefix('clientes')->middleware('organizacaoPj')->group(function () {
            Route::get('/', [ClientesController::class, 'index']);
            Route::post('/', [ClientesController::class, 'store']);
            Route::get('/{id}', [ClientesController::class, 'show']);
            Route::put('/{id}', [ClientesController::class, 'update']);
            Route::delete('/{id}', [ClientesController::class, 'destroy']);
        });

        Route::prefix('organizacoes')->group(function () {
            Route::get('/dados', [OrganizacoesController::class, 'show']);
            Route::put('/', [OrganizacoesController::class, 'update']);

            Route::middleware('organizacaoResponsavel')->group(function () {
                Route::delete('/pessoas/{id}', [OrganizacoesController::class, 'destroyPessoa']);
                Route::delete('/convites/{id}', [OrganizacoesController::class, 'destroyConvite']);
                Route::get('/integracoes/juno/link-cadastrar', [IntegracaoJunoController::class, 'getLinkCadastro']);
            });

            Route::delete('/{id}', [OrganizacoesController::class, 'destroy']);

            Route::prefix('integracoes')->group(function () {
                Route::get('/', [OrganizacoesController::class, 'integracoes']);
            });
        });

        Route::get('ufs', [UfsController::class, 'index']);
    });
    // });
});
