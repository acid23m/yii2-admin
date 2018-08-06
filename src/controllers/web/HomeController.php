<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 23.07.18
 * Time: 1:46
 */

namespace dashboard\controllers\web;

use dashboard\models\log\LogRecord;
use dashboard\models\log\LogSearch;
use dashboard\models\user\web\User;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\web\Response;
use yii\web\View;

/**
 * Class HomeController.
 *
 * @package dashboard\controllers\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class HomeController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['access']['rules'] = [
            [
                'allow' => true,
                'actions' => ['phpinfo'],
                'roles' => [User::ROLE_ADMIN]
            ],
            [
                'allow' => true,
                'actions' => ['clear-cache', 'clear-assets'],
                'roles' => [User::ROLE_AUTHOR]
            ],
            [
                'allow' => true,
                'actions' => ['download-log', 'clear-log', 'delete-log'],
                'roles' => [User::ROLE_SUPER]
            ],
            [
                'allow' => true,
                'roles' => ['@']
            ]
        ];

        return ArrayHelper::merge($behaviors, [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'clear-log' => ['POST'],
                    'delete-log' => ['POST']
                ]
            ]
        ]);
    }

    /**
     * Show home page.
     * @return string|View
     * @throws InvalidArgumentException
     */
    public function actionIndex()
    {
        // Log
        $logSearchModel = new LogSearch;
        $logDataProvider = $logSearchModel->search(
            \Yii::$app->getRequest()->getQueryParams()
        );

        return $this->render('index', compact('logSearchModel', 'logDataProvider'));
    }

    /**
     * Clear all logs.
     * @return Response
     */
    public function actionClearLog(): Response
    {
        LogRecord::deleteAll();
        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'log ochishen'));

        return $this->goHome();
    }

    /**
     * Delete log records.
     * @param string $ids List of ID
     * @return Response
     * @throws InvalidArgumentException
     */
    public function actionDeleteLog($ids): Response
    {
        $list = Json::decode($ids);

        LogRecord::deleteAll(['id' => $list]);
        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zapisi loga udaleni'));

        return $this->goHome();
    }

    /**
     * Download zip log file.
     * @throws ErrorException
     * @throws Exception
     */
    public function actionDownloadLog(): void
    {
        $tmp_dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . crc32('log');
        FileHelper::createDirectory($tmp_dir);

        $zip = new \ZipArchive;
        $zip_file = $tmp_dir . DIRECTORY_SEPARATOR . 'log.zip';

        if ($zip->open($zip_file, \ZipArchive::CREATE) !== true) {
            \Yii::$app->getSession()->setFlash('error', 'Zip error.');
            $this->goHome();
        }


        /**
         * @param string $app_part backend|frontend|remote
         */
        $add_log_to_zip = function (string $app_part) use (&$zip): void {
            $log_dir = \Yii::getAlias("@{$app_part}/runtime/logs");
            $log_files = FileHelper::findFiles($log_dir, [
                'filter' => function ($path) {
                    return preg_match('/app\.log[\.]+[\d]*$/', $path);
                }
            ]);
            foreach ($log_files as &$file) {
                $zip->addFile($file, $app_part . '/' . StringHelper::basename($file));
            }
        };

        // backend log
        $add_log_to_zip('backend');

        // frontend log
        $add_log_to_zip('frontend');

        // remote log
        $add_log_to_zip('remote');

        // console log
        $add_log_to_zip('console');


        $zip->close();

        \Yii::$app->getResponse()->sendFile($zip_file)->send();

        FileHelper::removeDirectory($tmp_dir);
    }

}
