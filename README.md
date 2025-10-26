# Пакет для ролевой системы проекттов на Symfony

## Руководство разработчика

### 1. Системные требования

- PHP >= 8.2
- Symfony >= 7.1

### 2. Установка

- в основном проекте в composer.json прописать
  ```
  "repositories": [
       {
            "type": "vcs",
            "url": "https://oauth2:JRB8vueyHk5JnBfUjNsb@gitlab.dev-u.ru/bundles/core_bundle.git"
        },
        {
            "type": "vcs",
            "url": "https://oauth2:JRB8vueyHk5JnBfUjNsb@gitlab.dev-u.ru/bundles/role_model_bundle.git"
        },
  ]
  ```
- выполнить команду `composer require Slcorp/core-bundle Slcorp/role-model-bundle`

### Services и UseCase

- работают через autowire и autoconfigure, прописанные в `config/services.yaml`

### ORM и миграции

1) В родительский проект будут добавлены

- `config/packages/doctrine_extensions.yaml`
- `config/packages/doctrine/role_model_bundle.php`
- `config/packages/doctrine_migrations/role_model_bundle.php`
- `config/packages/routes/role_model_bundle.php`

Конфиги бандла `.php` должны быть включены в основной проект.
Примеры реализации конфигов `.php` для родительского проекта находятся в директории `examples/config`

2) Для работы с ApIPlatform в `config/packages/api_platform.yaml` добавить

- mapping:
    - paths:
        - '%kernel.project_dir%/vendor/Slcorp/role-model-bundle/src/Domain/Entity'
        - '%kernel.project_dir%/vendor/Slcorp/role-model-bundle/src/Application/DTO'

3) Использоватение фильтра SQL:

```
'orm' => [
    'filters' => [
        'soft_delete_filter' => [
            'class' => 'Slcorp\RoleModelBundle\Infrastructure\Doctrine\Filter\SoftDeleteFilter',
            'enabled' => true,
        ],
    ],
]`,
```

- Все запросы через QueryBuilder и репозитории будут работать с этим фильтром. В результате - при поиске любых сущностей будут проигнорированы сущности, в которых есть поле delete = false.
- Для локального отключения работы фильтра можно использовать EntityManager, не забыв потом включить пример

```
  $this->entityManager->getFilters()->disable('soft_delete_filter');
  $deletedUser = $this->entityManager->getRepository(User::class)->find($id);
  $this->entityManager->getFilters()->enable('soft_delete_filter');
```


4) для работы JWT токена для ApiPlatform 

Заполнить в .env:

- JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
- JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
- JWT_PASSPHRASE=PASSPHRASE_JWT
- Сгенерировать или прописать в CI/CD
  `php bin/console lexik:jwt:generate-keypair --skip-if-exists`

В `security.yaml` родительского проекта прописать 

```
  providers:
    app_user_provider:
      entity:
        class: Slcorp\RoleModelBundle\Domain\Entity\User
        property: username

  firewalls:
    login:
      pattern: ^/api/role-model-bundle/users/login
      stateless: true
      json_login:
        check_path: /api/role-model-bundle/users/login
        username_path: username
        password_path: password
        success_handler: lexik_jwt_authentication.handler.authentication_success
        failure_handler: lexik_jwt_authentication.handler.authentication_failure
        provider: app_user_provider # Указываем провайдера для json_login
    api:
      pattern: ^/api/
      stateless: true
      provider: app_user_provider
      jwt: ~

  access_control:
    - { path: ^/api/role-model-bundle/users/login, roles: PUBLIC_ACCESS }
    - { path: ^/api/role-model-bundle/users/registration, roles: PUBLIC_ACCESS }
    - { path: ^/api, roles: ROLE_USER }  
```

В `lexik_jwt_authentication.yaml` родительского проекта прописать
```
lexik_jwt_authentication:
    secret_key: '%env(resolve:JWT_SECRET_KEY)%' # required for token creation
    public_key: '%env(resolve:JWT_PUBLIC_KEY)%' # required for token verification
    pass_phrase: '%env(JWT_PASSPHRASE)%' # required for token creation
    token_ttl: 3600 # in seconds, default is 3600
    api_platform:
        check_path: /api/role-model-bundle/users/login
        username_path: username
        password_path: password
```

В `api_platform.yaml` родительского проекта прописать
```
api_platform:
    title: Hello API Platform
    version: 1.0.0
    defaults:
        stateless: true
        cache_headers:
            vary: ['Content-Type', 'Authorization', 'Origin']
        # To enable or disable pagination for all resource collections.
        pagination_enabled: true
        # To allow the client to enable or disable the pagination.
        pagination_client_enabled: true
        # To allow the client to set the number of items per page.
        pagination_client_items_per_page: true
        # To allow the client to enable or disable the partial pagination.
        pagination_client_partial: true
        # The default number of items per page.
        pagination_items_per_page: 10
        # The maximum number of items per page.
        pagination_maximum_items_per_page: 200
        # To allow partial pagination for all resource collections.
        # This improves performances by skipping the `COUNT` query.
        pagination_partial: true
    mapping:
        paths:
            - '%kernel.project_dir%/vendor/Slcorp/role-model-bundle/src/Domain/Entity'
            - '%kernel.project_dir%/vendor/Slcorp/role-model-bundle/src/Application/DTO'
    swagger:
        versions: [ 3 ]
        api_keys:
            JWT:
                name: Authorization
                type: header
```

### Команды

- `make php-stan`  - `vendor/bin/phpstan analyse src tests`
- `make php-fix`  -  `vendor/bin/php-cs-fixer fix src && vendor/bin/php-cs-fixer fix tests`

### PS

*Редакция от 18/02/2025*
