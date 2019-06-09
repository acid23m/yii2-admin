<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/** @var yii\web\View $this */
/** @var yii\gii\generators\model\Generator $generator */
/** @var string $tableName full table name */
/** @var string $className class name */
/** @var string $queryClassName query class name */
/** @var yii\db\TableSchema $tableSchema */
/** @var array $properties list of properties (property => [type, name, comment]) */
/** @var string[] $labels list of attribute labels (name => label) */
/** @var string[] $rules list of validation rules */
/** @var array $relations list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;
<?php if (isset($properties['updated_at'])):  ?>

use dashboard\traits\DateTime;
use yii\behaviors\TimestampBehavior;
<?php endif ?>
<?php if (isset($properties['position'])):  ?>
use yii2tech\ar\position\PositionBehavior;
<?php endif ?>
<?php if (isset($properties['deleted'])):  ?>
use yii2tech\ar\softdelete\SoftDeleteBehavior;
<?php endif ?>
<?php if (isset($properties['slug'])):  ?>
use yii\behaviors\SluggableBehavior;
<?php endif ?>
<?php if ($generator->db !== 'db'): ?>
use yii\db\Connection;
<?php endif ?>

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($properties as $property => $data): ?>
 * @property <?= "{$data['type']} \${$property}"  . ($data['comment'] ? ' ' . \strtr($data['comment'], ["\n" => ' ']) : '') . "\n" ?>
<?php endforeach ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . \lcfirst($name) . "\n" ?>
<?php endforeach ?>
<?php endif ?>
<?php if (isset($properties['updated_at'])):  ?>
 *
 * @method void touch(string $attribute) Updates a timestamp attribute to the current timestamp
<?php endif ?>
<?php if (isset($properties['position'])):  ?>
 *
 * @method bool movePrev() Moves owner record by one position towards the start of the list.
 * @method bool moveNext() Moves owner record by one position towards the end of the list.
 * @method bool moveFirst() Moves owner record to the start of the list.
 * @method bool moveLast() Moves owner record to the end of the list.
 * @method bool moveToPosition(int $position) Moves owner record to the specific position. If specified position exceeds the total number of records, owner will be moved to the end of the list.
<?php endif ?>
<?php if (isset($properties['deleted'])):  ?>
 *
 * @method int|false softDelete() Marks the owner as deleted.
 * @method bool beforeSoftDelete() This method is invoked before soft deleting a record.
 * @method void afterSoftDelete() This method is invoked after soft deleting a record.
 * @method int|false restore() Restores record from "deleted" state, after it has been "soft" deleted.
 * @method bool beforeRestore() This method is invoked before record is restored from "deleted" state.
 * @method void afterRestore() This method is invoked after record is restored from "deleted" state.
 * @method int|false safeDelete() Attempts to perform regular [[BaseActiveRecord::delete()]], if it fails with exception, falls back to [[softDelete()]].
<?php endif ?>
 *
 * @package <?= $generator->ns . "\n" ?>
 */
class <?= $className ?> extends <?= '\\' . \ltrim($generator->baseClass, '\\') . "\n" ?>
{
<?php if (isset($properties['updated_at'])):  ?>
    use DateTime;

<?php endif ?>
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb(): Connection
    {
        return \Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif ?>

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
<?php if (isset($properties['updated_at'])):  ?>
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => [$this, 'getNowUTC']
            ],
<?php endif ?>
<?php if (isset($properties['position'])):  ?>
            'position' => [
                'class' => PositionBehavior::class,
                'positionAttribute' => 'position'
            ],
<?php endif ?>
<?php if (isset($properties['deleted'])):  ?>
            'softDelete' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'deleted' => true
                ],
                'invokeDeleteEvents' => false
            ],
<?php endif ?>
<?php if (isset($properties['slug'])):  ?>
            'slug' => [
                'class' => SluggableBehavior::class,
                'attribute' => 'title'
                //'attribute' => ['title', 'date']
                //'ensureUnique' => true
            ]
<?php endif ?>
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [<?= empty($rules) ? '' : ("\n            " . \implode(",\n            ", $rules) . ",\n        ") ?>];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * {@inheritdoc}
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find(): <?= $queryClassFullName . "\n" ?>
    {
        return new <?= $queryClassFullName ?>(static::class);
    }
<?php endif ?>

}
