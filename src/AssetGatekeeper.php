<?php

namespace artdepartment\craftassetgatekeeper;

use Craft;
use artdepartment\craftassetgatekeeper\models\Settings;
use craft\base\Element;
use craft\base\Event;
use craft\base\Model;
use craft\base\Plugin;
use craft\elements\Asset;
use craft\events\ModelEvent;
use craft\helpers\Assets;

/**
 * Asset Gatekeeper plugin
 *
 * @method static AssetGatekeeper getInstance()
 * @method Settings getSettings()
 * @author Art Department <support@artdepartment.co.uk>
 * @copyright Art Department
 * @license MIT
 */
class AssetGatekeeper extends Plugin
{

    public function init(): void
    {
        parent::init();
        $this->attachEventHandlers();
    }

    private function attachEventHandlers(): void
    {
        Event::on(
            Asset::class,
            Element::EVENT_BEFORE_SAVE,
            function (ModelEvent $modelEvent) {
                /** @var Asset $model */
                $model = $modelEvent->sender;
                $tmpPath = $model->tempFilePath;
                $fileSize = filesize($tmpPath);
                $volume = Craft::$app->volumes->getVolumeById($model->volumeId);
                $config = Craft::$app->config->getConfigFromFile('asset-gatekeeper');

                foreach($config['volumes'] as $volumeHandle => $volumeConfig) {
                    if ($volume->handle === $volumeHandle) {
                        // check allowed extensions
                        if (!in_array('*', $volumeConfig['allowed_extensions']) && !in_array($model->extension, $volumeConfig['allowed_extensions'])) {
                            $model->addError('extension', Craft::t('asset-gatekeeper', 'The file “{filename}” could not be uploaded, because the file extension “{extension}” is not allowed.', ['filename' => $model->filename, 'extension' => $model->extension]));
                            $modelEvent->isValid = false;
                            break;
                        }
                        // check max file size
                        $maxFileSize = $volumeConfig['max_filesize'] ?? Assets::getMaxUploadSize();
                        if ($maxFileSize < $fileSize) {
                            $model->addError('size', Craft::t('asset-gatekeeper', 'The file “{filename}” could not be uploaded, because the file size exceeds the maximum allowed size of {size} bytes.', ['filename' => $model->filename, 'size' => $maxFileSize]));
                            $modelEvent->isValid = false;
                            break;
                        }

                    }
                }
            }
        );
    }
}
