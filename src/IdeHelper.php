<?php
/**
 * Ide helper component for Yii framework 2.x.x version.
 *
 * Class IdeHelper
 */

namespace Mis\IdeHelper;

use Mis\IdeHelper\commands\IdeHelperController;
use yii\base\Component;
use yii\console\Application;
use yii\helpers\ArrayHelper;
use Yii;

class IdeHelper extends Component
{
    public $filename = '_ide_helper';

    public $format = 'php';

    public $rootDir;

    public $configFiles = [];

    protected $defaultConfigFiles = [
        'config/main.php',
        'config/main-local.php',
        'common/config/main.php',
        'common/config/main-local.php',
        'frontend/config/main.php',
        'frontend/config/main-local.php',
        'backend/config/main.php',
        'backend/config/main-local.php',
    ];

    /**
     * init method
     */
    public function init()
    {
        if(Yii::$app instanceof Application) {
            Yii::$app->controllerMap['ide-helper'] = IdeHelperController::class;
        }
    }

    protected function getRootDir()
    {
        return $this->rootDir ? rtrim($this->rootDir, '\/') : dirname(Yii::getAlias('@vendor'));
    }

    protected function readConfig()
    {
        $configFiles = array_merge($this->defaultConfigFiles, $this->configFiles);
        $config = [];
        $root = $this->getRootDir();
        foreach ($configFiles as $file) {
            if (is_file($root.DIRECTORY_SEPARATOR.$file)) {
                $config = ArrayHelper::merge($config, require($file));
            }
        }

        return $config;
    }

    protected function generateFilename()
    {
        return $this->getRootDir.DIRECTORY_SEPARATOR.$this->filename.'.'.$this->format;
    }

    public function generate()
    {
        $config = $this->readConfig();
        $string = '';
        foreach ($config['components'] as $name => $component) {
            if (isset($component['class'])) {
                $string .= PHP_EOL . ' * @property ' . $component['class'] . ' $' . $name;
            }
        }

        $helper = str_replace('* phpdoc', $string, file_get_contents(__DIR__.'/template.tpl'));

        file_put_contents($this->generateFilename(), $helper);
    }
}
