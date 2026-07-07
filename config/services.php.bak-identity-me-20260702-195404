<?php

declare(strict_types=1);

use App\Audit\AuditLogger;
use App\Bootstrap\Container;

use App\Http\Controller\AuthController;
use App\Http\Controller\HomeController;
use App\Http\Controller\UserAccountController;
use App\Http\Controller\DevelopmentController;

use App\Http\Routing\Router;

use App\Navigation\AdministrationNavigation;
use App\Navigation\AuthorizedMainMenu;
use App\Navigation\VerwaltungNavigation;

use App\Presentation\Navigation\HeaderDataProvider;
use App\Presentation\Templating\PhpRenderer;
use App\Presentation\Templating\Renderer;

use App\Repository\AccountProfileRepository;
use App\Repository\AreaRepository;
use App\Repository\AuditLogRepository;
use App\Repository\AuthorizedMenuRepository;
use App\Repository\EntityAuditRepository;
use App\Repository\IdentityAdministrationRepository;
use App\Repository\IdentityAuthorizationRepository;
use App\Repository\LoginEventRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\PageGroupAccessRepository;
use App\Repository\PageGroupRepository;
use App\Repository\PasswordResetRepository;
use App\Repository\PermissionGroupRepository;
use App\Repository\PersonAddressRepository;
use App\Repository\PersonContactRepository;
use App\Repository\PersonErasureRepository;
use App\Repository\PersonPermissionGroupRepository;
use App\Repository\PersonRepository;
use App\Repository\UserInvitationRepository;
use App\Repository\UserPasswordRepository;
use App\Repository\UserRepository;
use App\Repository\VerwaltungStatsRepository;

use App\Security\AccountSessionContext;
use App\Security\AdminSafetyService;
use App\Security\AuthorizationService;
use App\Security\CsrfGuard;
use App\Security\CsrfTokenManager;
use App\Security\IdentityAdminSafetyService;
use App\Security\RoutePermissionGuard;
use App\Security\RoutePermissionMap;
use App\Security\SessionAuth;

return [
    Renderer::class => static function (Container $container): Renderer {
        return new PhpRenderer(
            (string) $container->get('paths.views'),
            $container
        );
    },

    Router::class => static function (Container $container): Router {
        return new Router(
            $container,
            $container->get(RoutePermissionGuard::class)
        );
    },

    PDO::class => static function (Container $container): PDO {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';

        $name = getenv('DB_NAME')
            ?: getenv('DB_DATABASE')
            ?: 'vdbs';

        $user = getenv('DB_USER')
            ?: getenv('DB_USERNAME')
            ?: 'root';

        $pass = getenv('DB_PASS')
            ?: getenv('DB_PASSWORD')
            ?: '';

        $charset = getenv('DB_CHARSET') ?: 'utf8mb4';

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $host,
            $port,
            $name,
            $charset
        );

        try {
            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            ]);
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'Database connection failed: ' . $exception->getMessage()
            );
        }
    },

    /*
    |--------------------------------------------------------------------------
    | Basis-Repositories
    |--------------------------------------------------------------------------
    */

    AreaRepository::class => static function (Container $container): AreaRepository {
        return new AreaRepository($container->get(PDO::class));
    },

    MenuRepository::class => static function (Container $container): MenuRepository {
        return new MenuRepository($container->get(PDO::class));
    },

    MenuItemRepository::class => static function (Container $container): MenuItemRepository {
        return new MenuItemRepository($container->get(PDO::class));
    },

    UserRepository::class => static function (Container $container): UserRepository {
        return new UserRepository($container->get(PDO::class));
    },

    /*
    |--------------------------------------------------------------------------
    | Identity / Authorization-Repositories
    |--------------------------------------------------------------------------
    */

    IdentityAuthorizationRepository::class => static function (Container $container): IdentityAuthorizationRepository {
        return new IdentityAuthorizationRepository($container->get(PDO::class));
    },

    IdentityAdministrationRepository::class => static function (Container $container): IdentityAdministrationRepository {
        return new IdentityAdministrationRepository(
            $container->get(PDO::class),
            $container->get(IdentityAuthorizationRepository::class)
        );
    },

    /*
    |--------------------------------------------------------------------------
    | Verwaltung-Repositories
    |--------------------------------------------------------------------------
    */

    PersonRepository::class => static function (Container $container): PersonRepository {
        return new PersonRepository($container->get(PDO::class));
    },

    PersonContactRepository::class => static function (Container $container): PersonContactRepository {
        return new PersonContactRepository($container->get(PDO::class));
    },

    PersonAddressRepository::class => static function (Container $container): PersonAddressRepository {
        return new PersonAddressRepository($container->get(PDO::class));
    },

    PermissionGroupRepository::class => static function (Container $container): PermissionGroupRepository {
        return new PermissionGroupRepository($container->get(PDO::class));
    },

    PersonPermissionGroupRepository::class => static function (Container $container): PersonPermissionGroupRepository {
        return new PersonPermissionGroupRepository($container->get(PDO::class));
    },

    PageGroupRepository::class => static function (Container $container): PageGroupRepository {
        return new PageGroupRepository($container->get(PDO::class));
    },

    PageGroupAccessRepository::class => static function (Container $container): PageGroupAccessRepository {
        return new PageGroupAccessRepository($container->get(PDO::class));
    },

    AuditLogRepository::class => static function (Container $container): AuditLogRepository {
        return new AuditLogRepository($container->get(PDO::class));
    },

    UserInvitationRepository::class => static function (Container $container): UserInvitationRepository {
        return new UserInvitationRepository($container->get(PDO::class));
    },

    PersonErasureRepository::class => static function (Container $container): PersonErasureRepository {
        return new PersonErasureRepository($container->get(PDO::class));
    },

    VerwaltungStatsRepository::class => static function (Container $container): VerwaltungStatsRepository {
        return new VerwaltungStatsRepository($container->get(PDO::class));
    },

    AuthorizedMenuRepository::class => static function (Container $container): AuthorizedMenuRepository {
        return new AuthorizedMenuRepository(
            $container->get(PDO::class),
            $container->get(AuthorizationService::class)
        );
    },

    UserPasswordRepository::class => static function (Container $container): UserPasswordRepository {
        return new UserPasswordRepository($container->get(PDO::class));
    },

    PasswordResetRepository::class => static function (Container $container): PasswordResetRepository {
        return new PasswordResetRepository(
            $container->get(PDO::class),
            $container->get(UserPasswordRepository::class)
        );
    },

    AccountProfileRepository::class => static function (Container $container): AccountProfileRepository {
        return new AccountProfileRepository($container->get(PDO::class));
    },

    LoginEventRepository::class => static function (Container $container): LoginEventRepository {
        return new LoginEventRepository($container->get(PDO::class));
    },

    EntityAuditRepository::class => static function (Container $container): EntityAuditRepository {
        return new EntityAuditRepository($container->get(PDO::class));
    },

    /*
    |--------------------------------------------------------------------------
    | Security / Auth / CSRF
    |--------------------------------------------------------------------------
    */

    SessionAuth::class => static function (): SessionAuth {
        return new SessionAuth();
    },

    AuthorizationService::class => static function (Container $container): AuthorizationService {
        return new AuthorizationService(
            $container->get(SessionAuth::class),
            $container->get(UserRepository::class),
            $container->get(IdentityAuthorizationRepository::class)
        );
    },

    RoutePermissionMap::class => static function (): RoutePermissionMap {
        return new RoutePermissionMap();
    },

    RoutePermissionGuard::class => static function (Container $container): RoutePermissionGuard {
        return new RoutePermissionGuard(
            $container->get(AuthorizationService::class),
            $container->get(RoutePermissionMap::class)
        );
    },

    IdentityAdminSafetyService::class => static function (Container $container): IdentityAdminSafetyService {
        return new IdentityAdminSafetyService(
            $container->get(IdentityAdministrationRepository::class)
        );
    },

    AdminSafetyService::class => static function (Container $container): AdminSafetyService {
        return new AdminSafetyService(
            $container->get(PermissionGroupRepository::class),
            $container->get(PersonPermissionGroupRepository::class),
            $container->get(PageGroupAccessRepository::class)
        );
    },

    CsrfTokenManager::class => static function (): CsrfTokenManager {
        return new CsrfTokenManager();
    },

    CsrfGuard::class => static function (Container $container): CsrfGuard {
        return new CsrfGuard(
            $container->get(CsrfTokenManager::class)
        );
    },

    AccountSessionContext::class => static function (Container $container): AccountSessionContext {
        return new AccountSessionContext($container->get(PDO::class));
    },

    /*
    |--------------------------------------------------------------------------
    | Audit
    |--------------------------------------------------------------------------
    */

    AuditLogger::class => static function (Container $container): AuditLogger {
        return new AuditLogger(
            $container->get(AuditLogRepository::class),
            $container->get(SessionAuth::class),
            $container->get(PersonPermissionGroupRepository::class)
        );
    },

    /*
    |--------------------------------------------------------------------------
    | Navigation / Presentation
    |--------------------------------------------------------------------------
    */

    HeaderDataProvider::class => static function (Container $container): HeaderDataProvider {
        return new HeaderDataProvider(
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class),
            $container->get(UserRepository::class),
            $container->get(SessionAuth::class)
        );
    },

    AdministrationNavigation::class => static function (Container $container): AdministrationNavigation {
        return new AdministrationNavigation(
            $container->get(AuthorizationService::class)
        );
    },

    VerwaltungNavigation::class => static function (Container $container): VerwaltungNavigation {
        return new VerwaltungNavigation(
            $container->get(AuthorizationService::class)
        );
    },

    AuthorizedMainMenu::class => static function (Container $container): AuthorizedMainMenu {
        return new AuthorizedMainMenu(
            $container->get(AuthorizedMenuRepository::class)
        );
    },

    /*
    |--------------------------------------------------------------------------
    | Bestehende Controller-Factories
    |--------------------------------------------------------------------------
    | Die neuen Verwaltungscontroller können vom Router automatisch gebaut werden,
    | sobald ihre Dependencies oben im Container registriert sind.
    |--------------------------------------------------------------------------
    */

    HomeController::class => static function (Container $container): HomeController {
        return new HomeController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class)
        );
    },

    UserAccountController::class => static function (Container $container): UserAccountController {
        return new UserAccountController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class),
            $container->get(UserRepository::class),
            $container->get(SessionAuth::class)
        );
    },

    AuthController::class => static function (Container $container): AuthController {
        return new AuthController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(UserRepository::class),
            $container->get(SessionAuth::class)
        );
    },

    DevelopmentController::class => static function (Container $container): DevelopmentController {
        return new DevelopmentController(
            $container->get(Renderer::class),
            $container->get(AreaRepository::class),
            $container->get(MenuRepository::class),
            $container->get(MenuItemRepository::class)
        );
    },
];