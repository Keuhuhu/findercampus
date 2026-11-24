<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        VerifyEmail::createUrlUsing(static function ($user): string {
            $relativeUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ],
                absolute: false,
            );

            return URL::to($relativeUrl);
        });

        VerifyEmail::toMailUsing(static function ($user, string $verificationUrl): MailMessage {
            return (new MailMessage)
                ->subject('Verifikasi email FinderCampus')
                ->greeting('Halo '.$user->name)
                ->line('Klik tombol berikut untuk memverifikasi alamat email akun FinderCampus Anda.')
                ->action('Verifikasi Email', $verificationUrl)
                ->line('Tautan berlaku selama '.config('auth.verification.expire', 60).' menit.')
                ->line('Jika Anda tidak membuat akun FinderCampus, abaikan email ini.');
        });

        Blade::directive('mediaUrl', static function (string $expression): string {
            return "<?php echo e(app(\\App\\Services\\AssetStorage::class)->publicUrl($expression)); ?>";
        });

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
