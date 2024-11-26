    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Auth;

    Route::get('/', function () {
        return view('landing');
    });

    

    Route::controller(Auth\Admin\AuthController::class)->group( function () {
                Route::get('/register/admin', 'index')
                    ->middleware('auth', 'role:super_admin')
                    ->name('admin.register');

                Route::post('/register/admin', 'store')
                    ->middleware('auth', 'role:super_admin')
                    ->name('admin.store');
    });

    Route::get('/register', [Auth\Member\AuthController::class, 'index'])
                    ->middleware('guest')
                    ->name('register');

    Route::post('/register', [Auth\Member\AuthController::class, 'store'])
                    ->name('register.store');

    Route::get('/login', [Auth\LoginUserController::class, 'index'])
                    ->middleware('guest')
                    ->name('login.user');

    Route::post('/login', [Auth\LoginUserController::class, 'store'])
                    ->name('login.user.store');
    
    Route::get('/logout', [Auth\LogoutUserController::class, 'store'])
                    ->name('logout.store');