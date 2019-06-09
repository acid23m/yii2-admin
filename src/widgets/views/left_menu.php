<?php

use yii\helpers\Html;
use yiister\gentelella\widgets\Menu;

/** @var \yii\web\View $this */
/** @var array $items */

foreach ($items as $section_name => &$section_items) {
    echo '<div class="menu_section">';
    
    echo Html::tag('h3', $section_name);
    echo Menu::widget(['items' => $section_items]);

    echo '</div>';
}
