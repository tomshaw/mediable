# Mediable 🎥📸🎵📂 

Mediable is a light weight easy to use [Laravel](https://laravel.com)/[Livewire](https://livewire.laravel.com/) media browser. Inspired by Wordpress attachments this project aims to make file upload management a breeze. Mediable is awesome for injecting content into blog posts, carousels, product previews or similar applications.

## Installation

You can install the package via composer:

```bash
composer require tomshaw/mediable
```

Run the installation command.

```
php artisan mediable:install
```

Alternatively you can publish selected assets manually.

```
php artisan vendor:publish --provider="TomShaw\Mediable\Providers\MediableServiceProvider" --tag=images
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

## Props

The Mediable component accepts the following additional properties:

- `showPagination`: Controls the visibility of the pagination. Accepts a boolean value. Example usage: `:showPagination="false"`
- `showPerPage`: Controls the visibility of the per page option. Accepts a boolean value. Example usage: `:showPerPage="false"`
- `showOrderBy`: Controls the visibility of the order by option. Accepts a boolean value. Example usage: `:showOrderBy="false"`
- `showOrderDir`: Controls the visibility of the order direction option. Accepts a boolean value. Example usage: `:showOrderDir="false"`
- `showColumnWidth`: Controls the visibility of the column width option. Accepts a boolean value. Example usage: `:showColumnWidth="false"`
- `showSidebar`: Controls the visibility of the sidebar. Accepts a boolean value. Example usage: `:showSidebar="true"`

You can customize the component by setting these properties to `true` or `false` as needed.

Launching Mediable is done by dispatching the `mediable:open` event.

> Note: This is typically executed with a button click in your application.

```php
$this->dispatch('mediable:open');
```

Insert attachments directly into form inputs using the named parameter `id`. 

```php
$this->dispatch('mediable:open', id: 'description');
```

To handle selected attachments listen for the `mediable:on` event.

```php
on(['mediable:on' => function ($files) {
  // Handle selected files...
}]);
```

## Asset Publishing

When you publish the package's assets, they are copied to specific locations in your Laravel project:

1. **Configuration**: The `config.php` file is published to your project's `config` directory as `mediable.php`. You can modify this file to customize the package's configuration.

    ```bash
    config/mediable.php
    ```

2. **Views**: The package's views are published to the `views/vendor/mediable` directory in your project's `resources` directory. You can modify these views to customize the package's appearance.

    ```bash
    resources/views/vendor/mediable
    ```

3. **Images**: The package's images are published to the `vendor/mediable/images` directory in your project's `public` directory. You can replace these images with your own if you want to customize the package's visuals.

    ```bash
    public/vendor/mediable/images
    ```

To publish these assets, you can use the `vendor:publish` Artisan command:

```bash
php artisan vendor:publish --provider="Mediable\MediableServiceProvider"
```

You can also choose to publish only one type of asset by using the `--tag` option with either `config`, `views`, or `images`:

```bash
php artisan vendor:publish --provider="Mediable\MediableServiceProvider" --tag=config
```

## Validation

You can customize the allowable file types and sizes for uploads in the `mediable.php` config file.

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


## Requirements

- `Laravel 10` (https://laravel.com/) 
- `PHP 8.2` (https://php.net)

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
