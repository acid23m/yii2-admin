<?php

namespace dashboard\commands;

use dashboard\models\log\LogRecord;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;

/**
 * Deletes temporary files.
 *
 * @package dashboard\commands
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class CleanController extends Controller
{
    protected const APP_CONSOLE = 'console';
    protected const APP_BACK = 'backend';
    protected const APP_FRONT = 'frontend';
    protected const APP_REMOTE = 'remote';

    protected $app_types = [
        self::APP_CONSOLE,
        self::APP_BACK,
        self::APP_FRONT,
        self::APP_REMOTE
    ];

    /**
     * Cleans up.
     * @return int
     * @throws ErrorException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function actionIndex(): int
    {
        $total = \count($this->app_types) * 5;
        Console::startProgress(0, $total);

        $i = 0;
        foreach ($this->app_types as &$app_type) {
            $this->nullLog($app_type);
            Console::updateProgress(++ $i, $total);

            $this->delLogParts($app_type);
            Console::updateProgress(++ $i, $total);

            $this->delEmails($app_type);
            Console::updateProgress(++ $i, $total);

            $this->delDebug($app_type);
            Console::updateProgress(++ $i, $total);

            $this->delAssets($app_type);
            Console::updateProgress(++ $i, $total);
        }
        unset($app_type);

        Console::endProgress();
        $this->stdout("Done.\n", Console::FG_GREEN);
        $this->stdout("It can be ownership error. If so you must change owner, e.g.: sudo chown -R www-data:www-data .\n");

        return ExitCode::OK;
    }

    /**
     * Cleans up logs.
     * @return int
     * @throws InvalidArgumentException
     */
    public function actionLog(): int
    {
        $total = \count($this->app_types) * 2;
        Console::startProgress(0, $total);

        $i = 0;
        foreach ($this->app_types as $app_type) {
            $this->nullLog($app_type);
            Console::updateProgress(++ $i, $total);

            $this->delLogParts($app_type);
            Console::updateProgress(++ $i, $total);
        }

        Console::endProgress();
        $this->stdout("Done.\n", Console::FG_GREEN);
        $this->stdout("It can be ownership error. If so you must change owner, e.g.: sudo chown -R www-data:www-data .\n");

        return ExitCode::OK;
    }

    /**
     * Sets app.log empty.
     * @param string $app_type Application type (console|backend|frontend|remote)
     * @throws InvalidArgumentException
     */
    protected function nullLog($app_type): void
    {
        LogRecord::deleteAll();

        $log_file = \Yii::getAlias("@$app_type/runtime/logs/app.log");
        if (\file_exists($log_file)) {
            \file_put_contents($log_file, '', LOCK_EX);
        }
    }

    /**
     * Deletes additional log files (app.log.1, app.log.2 ..).
     * @param string $app_type Application type (console|backend|frontend|remote)
     * @throws InvalidArgumentException
     */
    protected function delLogParts(string $app_type): void
    {
        $log_dir = \Yii::getAlias("@$app_type/runtime/logs");

        if (\file_exists($log_dir)) {
            $log_files = FileHelper::findFiles($log_dir, [
                'filter' => static function ($path) {
                    $file_name = StringHelper::basename($path);
                    if (\preg_match('/[\d]$/', $file_name)) {
                        return true;
                    }

                    return false;
                }
            ]);

            foreach ($log_files as $log_file) {
                \unlink($log_file);
            }
        }
    }

    /**
     * @param string $app_type Application type (console|backend|frontend|remote)
     * @throws InvalidArgumentException
     */
    protected function delEmails(string $app_type): void
    {
        $emails = [];
        $email_dir = \Yii::getAlias("@$app_type/runtime/debug/mail");

        if (\file_exists($email_dir)) {
            $emails = FileHelper::findFiles($email_dir);
        }

        $email_dir = \Yii::getAlias("@$app_type/runtime/email");

        if (\file_exists($email_dir)) {
            $emails = ArrayHelper::merge($emails, FileHelper::findFiles($email_dir));
        }

        foreach ($emails as $email) {
            \unlink($email);
        }
    }

    /**
     * @param string $app_type Application type (console|backend|frontend|remote)
     * @throws InvalidArgumentException
     */
    protected function delDebug(string $app_type): void
    {
        $debug_dir = \Yii::getAlias("@$app_type/runtime/debug");

        if (\file_exists($debug_dir)) {
            $data_files = FileHelper::findFiles($debug_dir, [
                'filter' => static function ($path) {
                    $file_name = StringHelper::basename($path);
                    if (\preg_match('/^[0-9a-z]*.data$/', $file_name)) {
                        return true;
                    }

                    return false;
                }
            ]);

            foreach ($data_files as $data_file) {
                \unlink($data_file);
            }
        }
    }

    /**
     * @param string $app_type Application type (console|backend|frontend|remote)
     * @throws ErrorException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    protected function delAssets(string $app_type): void
    {
        $assets_dir = \Yii::getAlias("@$app_type/web/assets");

        if (\file_exists($assets_dir)) {
            FileHelper::removeDirectory($assets_dir);
            FileHelper::createDirectory($assets_dir, PERM_DIR);

            $gitignore = '*' . PHP_EOL . '!.gitignore' . PHP_EOL;
            $file = \fopen("$assets_dir/.gitignore", 'wb');
            \fwrite($file, $gitignore);
            \fclose($file);
            \chmod("$assets_dir/.gitignore", PERM_FILE);
        }
    }

}
