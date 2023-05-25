<?php
/**
 * Ide helper component for Yii framework 2.x.x version.
 *
 * Class IdeHelper
 */

namespace Mis\IdeHelper;

use Mis\IdeHelper\commands\IdeHelperController;
use Yii;
use yii\base\Component;
use yii\console\Application;
use yii\helpers\ArrayHelper;

class IdeHelper extends Component
{
    public $filename = '_ide_helper';

    public $format = 'php';

    public $rootDir;

    public $configFiles = [];

    protected $defaultConfigFiles = [
        'config/web.php',
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
     * init method.
     */
    public function init()
    {
        if (Yii::$app instanceof Application) {
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
        $config = ['components' => []];
        $root = $this->getRootDir();
        foreach ($configFiles as $file) {
            if (is_file($root . DIRECTORY_SEPARATOR . $file)) {
                $config = ArrayHelper::merge($config, require($root . DIRECTORY_SEPARATOR . $file));
            }
        }

        return $config;
    }

    protected function generateFilename()
    {
        return $this->getRootDir() . DIRECTORY_SEPARATOR . $this->filename . '.' . $this->format;
    }

    public function generate()
    {
        $config = $this->readConfig();
        $string = '';
        foreach ($config['components'] as $name => $component) {
            if (isset($component['class'])) {
                $string .= ' * @property ' . $component['class'] . ' $' . $name . "\n";
            }
        }

        $helper = str_replace(' * phpdoc', rtrim($string, "\n"), file_get_contents(__DIR__ . '/template.tpl'));

        file_put_contents($this->generateFilename(), $helper);
    }
}
