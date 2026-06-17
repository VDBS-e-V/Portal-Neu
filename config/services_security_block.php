<?php

declare(strict_types=1);

use App\Audit\AuditLogger;
use App\Bootstrap\Container;
use App\Repository\AuditLogRepository;
use App\Repository\PageGroupAccessRepository;
use App\Repository\PageGroupRepository;
use App\Repository\PermissionGroupRepository;
use App\Repository\PersonPermissionGroupRepository;
use App\Repository\UserRepository;
use App\Security\AdminSafetyService;
use App\Security\AuthorizationService;
use App\Security\SessionAuth;

return [
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

    AuthorizationService::class => static function (Container $container): AuthorizationService {
        return new AuthorizationService(
            $container->get(SessionAuth::class),
            $container->get(UserRepository::class),
            $container->get(PersonPermissionGroupRepository::class),
            $container->get(PageGroupAccessRepository::class)
        );
    },

    AdminSafetyService::class => static function (Container $container): AdminSafetyService {
        return new AdminSafetyService(
            $container->get(PermissionGroupRepository::class),
            $container->get(PersonPermissionGroupRepository::class),
            $container->get(PageGroupAccessRepository::class)
        );
    },

    AuditLogger::class => static function (Container $container): AuditLogger {
        return new AuditLogger(
            $container->get(AuditLogRepository::class),
            $container->get(SessionAuth::class),
            $container->get(PersonPermissionGroupRepository::class)
        );
    },
];