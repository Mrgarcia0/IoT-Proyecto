<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Base de datos compartida para varios computadores

Este proyecto, por defecto, usa **SQLite** (`database/database.sqlite`). Ese archivo está **ignorado por Git** (`database/.gitignore` contiene `*.sqlite*`), por lo que cada clon tendrá su propia BD local y los datos no se comparten.

Para que todos vean y generen los **mismos datos** desde cualquier computador, configura una **BD central** (recomendado **MySQL/MariaDB** o **PostgreSQL**) y apunta el `.env` de cada equipo a esa instancia.

### Pasos (MySQL/MariaDB recomendado)

1. Crea una instancia MySQL accesible por ambos equipos (servidor en la nube, contenedor en un PC con IP y VPN, etc.).
2. Crea BD y usuario:
   - BD: `iot_proyecto`
   - Usuario: `iot_user`, contraseña segura.
3. En cada PC, configura el `.env`:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=TU_HOST_O_IP
   DB_PORT=3306
   DB_DATABASE=iot_proyecto
   DB_USERNAME=iot_user
   DB_PASSWORD=tu_password_segura
   ```

4. Ejecuta migraciones (una sola vez para crear tablas):

   ```bash
   php artisan migrate --force
   php artisan db:seed --class=DeviceSeeder # opcional: carga dispositivos de ejemplo
   ```

Con esto, ambos equipos verán los mismos datos. La simulación y las lecturas que se generen se guardarán en la BD compartida.

### Nota sobre SQLite

Puedes quitar `*.sqlite*` de `database/.gitignore` y commitear `database.sqlite` para compartir un snapshot, pero **no** es recomendable: provoca conflictos y bloqueos al escribir desde varios equipos. Para colaboración, usa MySQL/PostgreSQL.

### Seguridad

- No commitees el `.env` con credenciales.
- Expón sólo el puerto de la BD necesario y usa VPN/Túneles seguros.
- Usa usuarios con permisos mínimos (lectura/escritura sobre la BD del proyecto).

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
