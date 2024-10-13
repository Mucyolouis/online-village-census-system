<div align="center">
  <img src="https://i.postimg.cc/4djrcJXx/logo.png" alt="Starter kit logo" width="200"/>

  [![Latest Version on Packagist](https://img.shields.io/packagist/v/riodwanto/superduper-filament-starter-kit.svg?style=flat-square)](https://packagist.org/packages/riodwanto/superduper-filament-starter-kit)
  [![Laravel](https://github.com/riodwanto/superduper-filament-starter-kit/actions/workflows/laravel.yml/badge.svg)](https://github.com/riodwanto/superduper-filament-starter-kit/actions/workflows/laravel.yml)
    [![Total Downloads](https://img.shields.io/packagist/dt/riodwanto/superduper-filament-starter-kit.svg?style=flat-square)](https://packagist.org/packages/riodwanto/superduper-filament-starter-kit)
</div>

<p align="center">
    Online Village Census System
this project is developed using Laravel 10 and filament 3. This is a Project i developed that would ease the workload for lower administration focused on citizen this project's scope limit will be the cell as it is the higher level that will be in charge of every village that it encompases this is merely a draft for creating systems that would allow citizen to do mostly their part without moving a lot and also allow the administratives of a village to approve/deny and have clear image of thei village citizens.
</p>






#### Getting Started

Clone the project using this command:

```bash
git clone https://github.com/riodwanto/online-village-census-system.git
```

Setup your env:

```bash
cd census
cp .env.example .env
```

Run migration & seeder:

```bash
php artisan migrate
php artisan db:seed
```

<p align="center">or</p>

```bash
php artisan migrate:fresh --seed
```

Generate key:

```bash
php artisan key:generate
```

Run :

```bash
npm run dev
OR
npm run build
```

```bash
php artisan serve
```

Now you can access with `/admin` path, using:

```bash
email: superadmin@admin.com
password: superadmin
```

*It's recommend to run below command as suggested in [Filament Documentation](https://filamentphp.com/docs/3.x/panels/installation#improving-filament-panel-performance) for improving panel perfomance.*

```bash
php artisan icons:cache
```

#### Language Generator
This project include lang generator. 
```
php artisan superduper:lang-translate [from] [to]
```
Generator will look up files inside folder `[from]`. Get all variables inside the file; create a file and translate using `translate.googleapis.com`.

This is what the translation process looks like.
```
❯ php artisan superduper:lang-translate en fr es

 🔔 Translate to 'fr'
 3/3 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100% -- ✅

 🔔 Translate to 'es'
 1/3 [▓▓▓▓▓▓▓▓▓░░░░░░░░░░░░░░░░░░░]  33% -- 🔄 Processing: page.php
```
###### Usage example
* Single output
```
php artisan superduper:lang-translate en fr
```
* Multiple output
```
php artisan superduper:lang-translate en es ar fr pt-PT pt-BR zh-CN zh-TW
```
###### If you are using json translation
```
php artisan superduper:lang-translate en fr --json
```

#### Plugins

These are [Filament Plugins](https://filamentphp.com/plugins) use for this project.

| **Plugin**                                                                                          | **Author**                                          |
| :-------------------------------------------------------------------------------------------------- | :-------------------------------------------------- |
| [Filament Spatie Media Library](https://github.com/filamentphp/spatie-laravel-media-library-plugin) | [Filament Official](https://github.com/filamentphp) |
| [Filament Spatie Settings](https://github.com/filamentphp/spatie-laravel-settings-plugin)           | [Filament Official](https://github.com/filamentphp) |
| [Filament Spatie Tags](https://github.com/filamentphp/spatie-laravel-tags-plugin)                   | [Filament Official](https://github.com/filamentphp) |
| [Shield](https://github.com/bezhanSalleh/filament-shield)                                           | [bezhansalleh](https://github.com/bezhansalleh)     |
| [Exceptions](https://github.com/bezhansalleh/filament-exceptions)                                   | [bezhansalleh](https://github.com/bezhansalleh)     |
| [Breezy](https://github.com/jeffgreco13/filament-breezy)                                            | [jeffgreco13](https://github.com/jeffgreco13)       |
| [Logger](https://github.com/z3d0x/filament-logger)                                                  | [z3d0x](https://github.com/z3d0x)                   |
| [Ace Code Editor](https://github.com/riodwanto/filament-ace-editor)                                 | [riodwanto](https://github.com/riodwanto)           |


### License

This project is provided under the [MIT License](LICENSE.md).

If you discover a bug, please [open an issue](/https://github.com/Mucyolouis/online-village-census-system/issues).

"# online-village-census-system" 
