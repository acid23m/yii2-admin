<?php
/**
 * This is the template for generating a module class file.
 */

/** @var yii\web\View $this */
/** @var yii\gii\generators\module\Generator $generator */

$className = $generator->moduleClass;
$pos = strrpos($className, '\\');
$ns = ltrim(substr($className, 0, $pos), '\\');
$className = substr($className, $pos + 1);

echo "<?php\n";
?>

namespace <?= $ns ?>;

use yii\i18n\PhpMessageSource;

/**
 * <?= $generator->moduleID ?> module definition class.
 */
final class <?= $className ?> extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = '<?= $generator->getControllerNamespace() ?>';

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->defaultRoute = 'default/index';

        \Yii::$app->getI18n()->translations["{$this->id}*"] = [
            'class' => PhpMessageSource::class,
            'basePath' => "@backend/modules/{$this->id}/messages",
            'fileMap' => [ // TODO: change or remove file map
                "{$this->id}/tr1" => 'tr1.php',
                "{$this->id}/tr2" => 'tr2.php'
            ]
        ];
    }

}
