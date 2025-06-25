# Asset Gatekeeper

A plugin to limit file size and type (via extensions) for individual volumes

## Requirements

This plugin requires Craft CMS 5.7.0 or later, and PHP 8.2 or later.

## Installation

Add the repository to your `composer.json` file:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/Art-Department/craft-asset-gatekeeper"
    }
]
```

Then run `composer update` to install the plugin.

Open your terminal and run the following commands:

```bash
# tell Composer to load the plugin
composer require art-department/craft-asset-gatekeeper

# tell Craft to install the plugin
./craft plugin/install asset-gatekeeper
```

### Configuration

Create a new config file at `config/asset-gatekeeper.php`

The following example shows how to configure the plugin to set maximum file sizes and allowed extensions for different asset volumes while using environment variables for flexibility and defaulting to sensible values:

```php
use craft\helpers\App;
use craft\helpers\Assets;

$defaultMaxFileSize = Assets::getMaxUploadSize(); // The Default max file size is set to the maximum upload size defined by Craft CMS

return [
    'volumes' => [
        'images' => [
            'max_filesize' => App::env('ASSET_GATEKEEPER_IMAGES_MAX_FILE_SIZE') ?: $defaultMaxFileSize,
            'allowed_extensions' => explode(',', App::env('ASSET_GATEKEEPER_IMAGES_ALLOWED_EXTENSIONS') ?: implode(',', Assets::getFileKinds()['image']['extensions'])), // Default image extensions
        ],
        'documents' => [
            'max_filesize' => App::env('ASSET_GATEKEEPER_DOCUMENTS_MAX_FILE_SIZE') ?: $defaultMaxFileSize,
            'allowed_extensions' => explode(',', App::env('ASSET_GATEKEEPER_DOCUMENTS_ALLOWED_EXTENSIONS') ?: 'pdf,doc,docx,xls,xlsx,ppt,pptx,txt'), // Default document extensions
        ],
    ],
];
```

You can set the environment variables in the .env like this:

```
ASSET_GATEKEEPER_IMAGES_MAX_FILE_SIZE=10485760 # 10 MB
ASSET_GATEKEEPER_IMAGES_ALLOWED_EXTENSIONS=jpg,jpeg,png,gif,webp,avif
ASSET_GATEKEEPER_DOCUMENTS_MAX_FILE_SIZE=5242880 # 5 MB
ASSET_GATEKEEPER_DOCUMENTS_ALLOWED_EXTENSIONS=pdf,doc,docx,xls,xlsx,ppt,pptx,txt
```

To allow all file types, you can set the allowed_extensions value to `*` :

```
ASSET_GATEKEEPER_DOCUMENTS_ALLOWED_EXTENSIONS=*
```

> [!NOTE]  
> You are free to name the .env variables whatever you like, as long as they match the ones used in the config file.