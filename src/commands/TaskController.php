<?php

namespace dashboard\commands;

use dashboard\models\task\TaskRecord;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii2tech\crontab\CronJob;
use yii2tech\crontab\CronTab;

/**
 * Class TaskController.
 *
 * @package dashboard\commands
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class TaskController extends Controller
{
    /**
     * Updates system crontab with saved tasks.
     * @return int
     */
    public function actionReload(): int
    {
        // first remove all tasks
        $tasks = TaskRecord::find()->all();
        $jobs = [];
        foreach ($tasks as $task) {
            $job = new CronJob;
            $job->setAttributes(
                $task->getAttributes()
            );
            $jobs[] = $job;
        }
        $tab = new CronTab;
        $tab->setJobs($jobs)->remove();

        // saves new tasks
        $tasks = TaskRecord::find()->published()->all();
        $jobs = [];
        foreach ($tasks as $task) {
            $job = new CronJob;
            $job->setAttributes(
                $task->getAttributes()
            );
            $jobs[] = $job;
        }
        $tab = new CronTab([
            'headLines' => [
                'SHELL=/bin/bash',
                'PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin:/app'
            ]
        ]);
        $tab->setJobs($jobs)->apply();

        $this->stdout("Done.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

}
