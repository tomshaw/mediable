# Mediable 🎥📸🎵📂 

Mediable is a light weight easy to use Laravel Livewire Media Manager. Mediable is awesome for injecting content into blog posts, carousels, product previews or similar applications.

![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/tomshaw/mediable/run-tests.yml?branch=master&style=flat-square&label=tests)
![issues](https://img.shields.io/github/issues/tomshaw/mediable?style=flat&logo=appveyor)
![forks](https://img.shields.io/github/forks/tomshaw/mediable?style=flat&logo=appveyor)
![stars](https://img.shields.io/github/stars/tomshaw/mediable?style=flat&logo=appveyor)
[![GitHub license](https://img.shields.io/github/license/tomshaw/mediable)](https://github.com/tomshaw/mediable/blob/master/LICENSE)

> For a complete demonstration see the [Mediable Demo](https://github.com/tomshaw/mediable-demo) repository.

### Screenshots
Here's an example of what this project looks like.

#### Screenshot
![Mediable](https://raw.github.com/tomshaw/mediable/master/docs/mediable.jpg)

## Installation

You can install the package via composer:

```bash
composer require tomshaw/mediable
```

Mediable comes with both install and update commands.

```bash
php artisan mediable:install
```

```bash
php artisan mediable:update
```

Run the included database migration.

> This creates an attachments table that stores upload information.

```bash
php artisan migrate
```

Add Mediable styles and scripts directives to your layout.

```html
@vite(['resources/css/app.css', 'resources/js/app.js'])

@mediableStyles
@mediableScripts
```

Make sure your `.env` `APP_URL` is correctly set.

```env
APP_URL=https://mydomain.com
```

Finally make uploaded files accessible from the web.

```bash
php artisan storage:link
```

## Usage

Add the Mediable component to your blade template.

> Boolean options can be provided by only specifying the key.

```html
<livewire:mediable fullScreen />
```

Launching Mediable is done by dispatching the `mediable.open` event.

> This is typically executed with a button click.

```php
$this->dispatch('mediable.open');
```

Insert attachments directly into form inputs using PHP 8 named parameters. 

> This example launches the modal with the intention of injecting attachments directly into an html input that has an `id` of `description`.

```php
$this->dispatch('mediable.open', id: 'description');
```

Use the `mediable.on` event to handle selected attachments.

```php
on(['mediable.on' => function ($files) {
  // Handle selected files...
}]);
```

## Validation

You can customize allowable file types and max file size in the `mediable.php` config file.

```php
'validation' => [
    'files.*' => 'required|mimes:jpeg,png,jpg,gif,mp3,mp4,m4a,ogg,wav,webm,avi,mov,wmv,txt,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:10240',
],
```

The `mimes` rule specifies the allowable file types. To add a new file type, simply add its mime type to the list. For example, to allow SVG files, you would change it to:

```php
'mimes:jpeg,png,jpg,gif,mp3,mp4,m4a,ogg,wav,webm,avi,mov,wmv,txt,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,svg'
```

The `max` rule specifies the maximum file size, in kilobytes. To change the maximum file size, simply change the number. For example, to allow files up to 50MB, you would change it to:

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

### Image Conversion Settings

Mediable can automatically create WebP and AVIF versions of your image uploads. You can control this behavior with the following environment variables:

- `MEDIABLE_CREATE_WEBP`: Set this to `true` to create a WebP version of each image upload, or `false` to disable this feature. By default, this is set to `true`.

- `MEDIABLE_CREATE_AVIF`: Set this to `true` to create an AVIF version of each image upload, or `false` to disable this feature. By default, this is set to `true`.

You can also control the quality of the WebP and AVIF versions with the following environment variables:

- `MEDIABLE_WEBP_QUALITY`: Set this to any integer between 0 and 100 to control the quality of the WebP versions. A higher number means better quality but larger file size. By default, this is set to 80.

- `MEDIABLE_AVIF_QUALITY`: Set this to any integer between 0 and 100 to control the quality of the AVIF versions. A higher number means better quality but larger file size. By default, this is set to 80.

Here's an example of how you might set these environment variables in your `.env` file:

```env
MEDIABLE_CREATE_WEBP=true 
MEDIABLE_CREATE_AVIF=true 
MEDIABLE_WEBP_QUALITY=80 
MEDIABLE_AVIF_QUALITY=80
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). See [License File](LICENSE) for more information.

```

                 _ _       _     _      
  /\/\   ___  __| (_) __ _| |__ | | ___ 
 /    \ / _ \/ _` | |/ _` | '_ \| |/ _ \
/ /\/\ \  __/ (_| | | (_| | |_) | |  __/
\/    \/\___|\__,_|_|\__,_|_.__/|_|\___|
                                        
```
