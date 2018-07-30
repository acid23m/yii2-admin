<?php

namespace dashboard\widgets;

use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Alert;
use yii\bootstrap\Widget;

/**
 * Alert widget renders a message from session flash. All flash messages are displayed
 * in the sequence they were assigned using setFlash. You can set message as following:
 *
 * - \Yii::$app->getSession()->setFlash('push-error', 'This is the message');
 * - \Yii::$app->getSession()->setFlash('push-success', 'This is the message');
 * - \Yii::$app->getSession()->setFlash('push-info', 'This is the message');
 *
 * @package dashboard\widgets
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class Push extends Widget
{
    /**
     * This array is setup as $key => $value, where:
     * - $key is the name of the session flash variable
     * - $value is the bootstrap alert type (i.e. default, success, info, warning), icon and title
     * @var array the alert types configuration for the flash messages.
     */
    public $alertTypes = [
        'push-danger' => ['alert-danger', 'exclamation-triangle'],
        'push-success' => ['alert-success', 'thumbs-up'],
        'push-info' => ['alert-info', 'info-circle'],
        'push-warning' => ['alert-warning', 'exclamation-circle']
    ];
    /**
     * @var array the options for rendering the close button tag.
     */
    public $closeButton = [];

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function init(): void
    {
        parent::init();

        $session = \Yii::$app->getSession();
        $flashes = $session->getAllFlashes();
        $appendCss = $this->options['class'] ?? '';

        foreach ($flashes as $type => $message) {
            if (isset($this->alertTypes[$type])) {
                // initialize css class for each alert box
                $this->options['class'] = "{$this->alertTypes[$type][0]} $appendCss";

                // assign unique id to each alert box
                $this->options['id'] = $this->getId() . '-' . $type;

                echo Alert::widget([
                    'body' => FA::icon($this->alertTypes[$type][1]) . ' ' . $message,
                    'closeButton' => $this->closeButton,
                    'options' => $this->options
                ]);

                $session->removeFlash($type);
            }
        }
    }

}
