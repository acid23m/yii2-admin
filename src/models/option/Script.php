<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 17.08.18
 * Time: 0:46
 */

namespace dashboard\models\option;

use yii\base\Model;

/**
 * Class Script.
 *
 * @package dashboard\models\option
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class Script extends Model
{
    public const HEAD_FILE_PATH = '@common/data/.head.script';
    public const BODY_FILE_PATH = '@common/data/.body.script';

    /**
     * @var string Scripts in the <head> section
     */
    public $head_script;
    /**
     * @var string Scripts in the page bottom before </body>
     */
    public $body_script;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $head_file_path = \Yii::getAlias(self::HEAD_FILE_PATH);
        $this->head_script = $this->readFile($head_file_path);

        $body_file_path = \Yii::getAlias(self::BODY_FILE_PATH);
        $this->body_script = $this->readFile($body_file_path);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['head_script', 'body_script'], 'trim'],
            [['head_script', 'body_script'], 'string']
        ];
    }

    /**
     * Save scripts.
     * @return bool
     * @throws \yii\base\InvalidArgumentException
     */
    public function save(): bool
    {
        $head_file_path = \Yii::getAlias(self::HEAD_FILE_PATH);
        $write_head_script = $this->writeFile($head_file_path, $this->head_script);

        $body_file_path = \Yii::getAlias(self::BODY_FILE_PATH);
        $write_body_script = $this->writeFile($body_file_path, $this->body_script);

        return $write_head_script && $write_body_script;
    }

    /**
     * Read file.
     * @param string $path Path to file
     * @return string
     */
    public function readFile(string $path): string
    {
        $content = '';

        $mode = 'rb';
        if (!file_exists($path)) {
            $mode = 'w+b';
        }

        try {
            $file = fopen($path, $mode);
            while (!feof($file)) {
                $content .= fread($file, 8192);
            }
            fclose($file);
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage());
        }

        return $content;
    }

    /**
     * Write to file.
     * @param string $path Path to file
     * @param string $content Scripts
     * @return bool
     */
    protected function writeFile(string $path, string $content): bool
    {
        try {
            $file = fopen($path, 'wb');
            fwrite($file, $content);
            fclose($file);

            return true;
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage());

            return false;
        }
    }

}
