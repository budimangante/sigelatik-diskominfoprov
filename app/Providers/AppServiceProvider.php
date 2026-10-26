<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Model::shouldBeStrict(
        //     ! app()->isProduction()
        // );
        // $this->configureDefaults();
        if (env('BKD_MOCK_SSO', false) || config('services.bkd.mock_sso', false)) {
            Http::fake([
                '*' => function (\Illuminate\Http\Client\Request $request) {
                    $body = $request->data();

                    // Simulasi NIP pegawai valid
                    if (($body['nip'] ?? '') === '199501012022011001' && ($body['password'] ?? '') === 'secret123') {
                        return Http::response([
                            'status' => 'success',
                            'data'   => [
                                'nip'      => '199501012022011001',
                                'nama'     => 'Ahmad Pegawai BKD',
                                'instansi' => 'Badan Kepegawaian Daerah',
                                'role_bkd' => 'teknisi',
                            ]
                        ], 200);
                    } 
                    if (($body['nip'] ?? '') === '198504102010012003' && ($body['password'] ?? '') === 'secret456') {
                        return Http::response([
                            'status' => 'success',
                            'data'   => [
                                'nip'      => '198504102010012003',
                                'nama'     => 'Siti Pegawai BKD',
                                'instansi' => 'Badan Kepegawaian Daerah',
                                'role_bkd' => 'dinas',
                            ]
                        ], 200);
                    }
                    if (($body['nip'] ?? '') === '198801152015031002' && ($body['password'] ?? '') === 'secret789') {
                        return Http::response([
                            'status' => 'success',
                            'data'   => [
                                'nip'      => '198801152015031002',
                                'nama'     => 'Ahmad Fauzi, S.Kom',
                                'instansi' => 'Bidang TIK Diskominfo',
                                'role_bkd' => 'bidang',
                            ]
                        ], 200);
                    }

                    // Kredensial salah
                    return Http::response([
                        'status'  => 'error',
                        'message' => 'NIP atau password SSO BKD salah.',
                    ], 401);
                },
            ]);
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
