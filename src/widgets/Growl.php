<?php

namespace dashboard\widgets;

use kartik\growl\Growl as kGrowl;
use yii\base\Widget;

/**
 * Growl widget renders a message from session flash. All flash messages are displayed
 * in the sequence they were assigned using setFlash. You can set message as following:
 *
 * - \Yii::$app->getSession()->setFlash('error', 'This is the message');
 * - \Yii::$app->getSession()->setFlash('success', 'This is the message');
 * - \Yii::$app->getSession()->setFlash('info', 'This is the message');
 *
 * @package dashboard\widgets
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
final class Growl extends Widget
{
    /**
     * This array is setup as $key => $value, where:
     * - $key is the name of the session flash variable
     * - $value is the array of the bootstrap alert type (i.e. danger, success, info, warning) and icon
     * @var array the alert types configuration for the flash messages.
     */
    public $alertTypes = [
        'error' => ['alert-danger', 'fa-bug'],
        'danger' => ['alert-danger', 'fa-exclamation-triangle'],
        'success' => ['alert-success', 'fa-thumbs-up'],
        'info' => ['alert-info', 'fa-info-circle'],
        'warning' => ['alert-warning', 'fa-exclamation-circle']
    ];
    /**
     * @var array the options for rendering the close button tag.
     */
    public $closeButton = [];
    /**
     * @var array the HTML attributes for the widget container tag.
     */
    public $options = [];

    /**
     * {@inheritdoc}
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

                echo kGrowl::widget([
                    'type' => $this->alertTypes[$type][0],
                    'icon' => $this->alertTypes[$type][1] . ' fa',
                    'body' => $message,
                    'delay' => 500,
                    'pluginOptions' => [
                        'allow_dismiss' => false,
                        'placement' => [
                            'from' => 'top',
                            'align' => 'right'
                        ]
                    ],
                    'closeButton' => $this->closeButton,
                    'options' => $this->options
                ]);

                $session->removeFlash($type);
            }
        }
    }

}
