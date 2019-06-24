<?php

namespace dashboard\controllers\web;

use dashboard\models\index\SearchIndex;
use dashboard\models\index\SearchIndexJob;
use dashboard\models\log\LogRecord;
use dashboard\models\log\LogSearch;
use dashboard\models\sitemap\SitemapGenerator;
use dashboard\models\sitemap\SitemapJob;
use dashboard\models\user\UserIdentity;
use dashboard\models\user\web\User;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\queue\Queue;
use yii\web\ErrorAction;
use yii\web\Response;

/**
 * Class HomeController.
 *
 * @package dashboard\controllers\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class HomeController extends BaseController
{
    /**
     * {@inheritdoc}
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
                'actions' => ['clear-cache', 'clear-assets', 'sitemap'],
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
                    'clear-log' => ['post'],
                    'delete-log' => ['post']
                ]
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        $actions = [
            'error' => ErrorAction::class
        ];

        return ArrayHelper::merge(parent::actions(), $actions);
    }

    /**
     * Shows home page.
     * @return string
     * @throws InvalidArgumentException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionIndex(): string
    {
        // Notes
        /** @var UserIdentity $user */
        $user = \Yii::$app->getUser()->getIdentity();
        if ($user->load(\Yii::$app->getRequest()->post())) {
            $user->password = null;
            if ($user->update()) {
                \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'zametki sohraneny'));
            } else {
                $errors = $user->getErrorSummary(true);
                $errors_str = Inflector::sentence($errors, '<br>', '<br>', '<br>');
                \Yii::$app->getSession()->setFlash('error', $errors_str);
            }
        }

        // Search index
        /** @var SearchIndex $search_index */
        $search_index = \Yii::createObject(SearchIndex::class);
        $search_index_is_active = $search_index->is_active;

        // Log
        $logSearchModel = new LogSearch;
        $logDataProvider = $logSearchModel->search(
            \Yii::$app->getRequest()->getQueryParams()
        );

        return $this->render('index', \compact('search_index_is_active', 'logSearchModel', 'logDataProvider'));
    }

    /**
     * Clears all logs.
     * @return Response
     */
    public function actionClearLog(): Response
    {
        LogRecord::deleteAll();
        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'log ochishen'));

        return $this->goHome();
    }

    /**
     * Deletes log records.
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
     * Downloads zip log file.
     * @throws ErrorException
     * @throws Exception
     */
    public function actionDownloadLog(): void
    {
        $tmp_dir = \sys_get_temp_dir() . DIRECTORY_SEPARATOR . \crc32('log');
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
        $add_log_to_zip = static function (string $app_part) use (&$zip): void {
            $log_dir = \Yii::getAlias("@{$app_part}/runtime/logs");
            $log_files = FileHelper::findFiles($log_dir, [
                'filter' => static function ($path) {
                    return \preg_match('/app\.log[\.]+[\d]*$/', $path);
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

    /**
     * Php info.
     * @return string
     * @throws InvalidArgumentException
     */
    public function actionPhpinfo(): string
    {
        return $this->renderPartial('phpinfo');
    }

    /**
     * Clears application cache.
     * @return Response
     */
    public function actionClearCache(): Response
    {
        \clearstatcache(true);

        if (\extension_loaded('Zend OPcache')) {
            \opcache_reset();
        }

        $cache = \Yii::$app->getCache();

        if ($cache === null) {
            \Yii::$app->getSession()->setFlash('warning', \Yii::t('dashboard', 'kesh ne skonfigurirovan'));

            return $this->goHome();
        }

        $cache->flush();
        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'kesh ochishen'));

        return $this->goHome();
    }

    /**
     * Clears assets folders.
     * @return Response
     * @throws ErrorException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function actionClearAssets(): Response
    {
        $path1 = \Yii::getAlias('@frontend/web/assets');
        $path2 = \Yii::getAlias('@backend/web/assets');

        /**
         * Create .gitignore file.
         * @param string $path
         */
        $create_gitignore = static function (string $path): void {
            $content = '*' . PHP_EOL . '!.gitignore' . PHP_EOL;
            $file = \fopen($path . '/.gitignore', 'wb');
            \fwrite($file, $content);
            \fclose($file);
            \chmod($path . '/.gitignore', PERM_FILE);
        };

        FileHelper::removeDirectory($path1);
        FileHelper::removeDirectory($path2);

        FileHelper::createDirectory($path1, PERM_DIR);
        FileHelper::createDirectory($path2, PERM_DIR);

        $create_gitignore($path1);
        $create_gitignore($path2);

        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'papka resursov ochishena'));

        return $this->goHome();
    }

    /**
     * Creates sitemap files.
     * @return Response
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws \InvalidArgumentException
     */
    public function actionSitemap(): Response
    {
        /** @var Queue $queue */
        $queue = \Yii::$app->get('queue', false);
        if ($queue instanceof Queue) {
            $queue->push(new SitemapJob);
        } else {
            SitemapGenerator::write();
        }

        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'gotovo'));

        return $this->goHome();
    }

    /**
     * Updates search index.
     * @return Response
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws \S2\Rose\Exception\LogicException
     * @throws \S2\Rose\Exception\UnknownException
     * @throws \S2\Rose\Storage\Exception\InvalidEnvironmentException
     */
    public function actionSearchIndex(): Response
    {
        /** @var Queue $queue */
        $queue = \Yii::$app->get('queue', false);
        if ($queue instanceof Queue) {
            $queue->push(new SearchIndexJob);
        } else {
            /** @var SearchIndex $search_index */
            $search_index = \Yii::createObject(SearchIndex::class);
            if ($search_index->is_active) {
                $search_index->getStorage()->erase(); // clear
                $search_index->index(); // index
            }
        }

        \Yii::$app->getSession()->setFlash('success', \Yii::t('dashboard', 'gotovo'));

        return $this->goHome();
    }

}
