<?php
/**
 * Command for manage ide helper component for Yii framework 2.x.x version.
 */
namespace Mis\IdeHelper\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class IdeHelperController extends Controller
{
    public $defaultAction = 'generate';

    public function actionGenerate()
    {
        $this->stdout('Starting generation...'.PHP_EOL, Console::FG_CYAN);
        Yii::$app->ideHelper->generate();
        $this->stdout('Done generation...'.PHP_EOL, Console::FG_GREEN);
    }
}