<?php

namespace App\Providers\Filament;

use App\Filament\Resources\AcademicYearResource\Widgets\AcademicYearWidget;
use App\Filament\Resources\GradeResource\Widgets\GradeWidget;
use App\Filament\Resources\StudentResource\Widgets\StudentWidget;
use App\Filament\Resources\TeacherResource\Widgets\TeacherWidget;
use App\Filament\Resources\UserResource\Widgets\UserOnlineWidget;
use App\Http\Middleware\UpdateLastActivity;
use App\Providers\Filament\Auth\Login;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Rmsramos\Activitylog\ActivitylogPlugin;
use Rmsramos\Activitylog\Resources\ActivitylogResource;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->colors([
                'danger' => Color::Red,         // Merah untuk menandakan error atau bahaya
                'gray' => Color::Zinc,          // Warna netral untuk komponen seperti panel
                'info' => Color::Blue,          // Biru untuk info, cocok dengan Purple
                'primary' => Color::Purple,     // Warna utama Purple
                'success' => Color::Green,      // Hijau untuk status sukses atau berhasil
                'warning' => Color::Amber,      // Kuning untuk status peringatan
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                AcademicYearWidget::class,
                UserOnlineWidget::class,
                TeacherWidget::class,
                StudentWidget::class,
                GradeWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                UpdateLastActivity::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                // ActivitylogPlugin::make()
                //     ->resource(ActivitylogResource::class),
                FilamentEditProfilePlugin::make()
                    ->setSort(1)
                    ->setIcon('heroicon-o-user')
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldShowAvatarForm(),
                    FilamentApexChartsPlugin::make()
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('5s')
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
