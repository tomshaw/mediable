# Mediable 🎥📸🎵📂 

Mediable is a light weight easy to use Laravel Livewire Media Manager. Mediable is awesome for injecting content into blog posts, carousels, product previews or similar applications.

![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/tomshaw/mediable/run-tests.yml?branch=master&style=flat-square&label=tests)
![issues](https://img.shields.io/github/issues/tomshaw/mediable?style=flat&logo=appveyor)
![forks](https://img.shields.io/github/forks/tomshaw/mediable?style=flat&logo=appveyor)
![stars](https://img.shields.io/github/stars/tomshaw/mediable?style=flat&logo=appveyor)
[![GitHub license](https://img.shields.io/github/license/tomshaw/mediable)](https://github.com/tomshaw/mediable/blob/master/LICENSE)

## Installation

You can install the package via composer:

```bash
composer require tomshaw/mediable
```

Run the installation command.

```
php artisan mediable:install
```

After updates run the update command.

```
php artisan mediable:update
```

When you install the application assets are copied to the following locations:

1. The config file is published to your project's config directory as `mediable.php`. 

    ```bash
    config/mediable.php
    ```

2. Views are published to the `views/vendor/mediable` directory in your project's `resources` directory.

    ```bash
    resources/views/vendor/mediable
    ```

3. Images are published to the `vendor/mediable/images` directory in your project's `public` directory.

    ```bash
    public/vendor/mediable/images
    ```

Run the database migration

```
php artisan migrate.
```

Make sure to add the styles and scripts directives to your layout.

```html
@vite(['resources/css/app.css', 'resources/js/app.js'])

@mediableStyles
@mediableScripts
```

## Usage

Step one is to add Mediable somewhere in your blade template.

> Note: The following parameters are optional. Boolean options can be provided by only specifying the key.

```html
<livewire:mediable theme="tailwind" fullScreen />
```

Launching Mediable is done by dispatching the `mediable:open` event.

> Note: This is typically executed with a button click in your application.

```php
$this->dispatch('mediable:open');
```

Insert attachments directly into form inputs typically a `textarea` using the named parameter `id`. 

> This example launches the modal with the intention of injecting attachments directly into an html input that has an `id` of `description`.

```php
$this->dispatch('mediable:open', id: 'description');
```

To handle selected attachments listen for the `mediable:on` event.

```php
on(['mediable:on' => function ($files) {
  // Handle selected files...
}]);
```

## Validation

You can customize allowable file types and max file size in the `mediable.php` config file.

Here's how:

1. Open the `mediable.php` config file.

2. Look for the `validation` array. It should look something like this:

    ```php
    'validation' => [
        'files.*' => 'required|mimes:jpeg,png,jpg,gif,mp3,mp4,m4a,ogg,wav,webm,avi,mov,wmv,txt,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:10240',
    ],
    ```

3. The `mimes` rule specifies the allowable file types. To add a new file type, simply add its mime type to the list. For example, to allow SVG files, you would change it to:

    ```php
    'mimes:jpeg,png,jpg,gif,mp3,mp4,m4a,ogg,wav,webm,avi,mov,wmv,txt,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,svg'
    ```

4. The `max` rule specifies the maximum file size, in kilobytes. To change the maximum file size, simply change the number. For example, to allow files up to 50MB, you would change it to:

    ```php
    'max:51200'
    ```

## Storage Disk

You can configure the storage disk used for file uploads in the `mediable.php` config file. The `disk` option is used to specify the disk name:

```php
'disk' => env('FILESYSTEM_DRIVER', 'public'),
```

The value of `disk` is the key of `disks` in your Laravel application's `config/filesystems.php` file. By default, it uses the disk specified by the `FILESYSTEM_DRIVER` environment variable, or 'public' if the environment variable is not set.

You can change the `disk` option to use a different disk for file uploads. For example, to use the 's3' disk, you can set `disk` to 's3':

```php
'disk' => 's3',
```

Remember to configure the chosen disk correctly in your `config/filesystems.php` file and to clear your config cache after making changes by running `php artisan config:clear` in your terminal.

## Component Props

The Mediable component accepts the following additional properties:

- `showPagination`: Controls the visibility of the pagination. Example usage: `:showPagination="false"`
- `showPerPage`: Controls the visibility of the per page option. Example usage: `:showPerPage="false"`
- `showOrderBy`: Controls the visibility of the order by option. Example usage: `:showOrderBy="false"`
- `showOrderDir`: Controls the visibility of the order direction option. Example usage: `:showOrderDir="false"`
- `showColumnWidth`: Controls the visibility of the column width option. Example usage: `:showColumnWidth="false"`
- `showSidebar`: Controls the visibility of the sidebar. Example usage: `:showSidebar="true"`

You can customize the component by setting these properties to `true` or `false` as needed.

## Requirements

The package is compatible with Laravel 10 and PHP 8.1.

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). See [License File](LICENSE) for more information.
