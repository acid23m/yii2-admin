<?php
/**
 * This is the template for generating the ActiveQuery class.
 */

/** @var yii\web\View $this */
/** @var yii\gii\generators\model\Generator $generator */
/** @var string $tableName full table name */
/** @var string $className class name */
/** @var yii\db\TableSchema $tableSchema */
/** @var string[] $labels list of attribute labels (name => label) */
/** @var string[] $rules list of validation rules */
/** @var array $relations list of relations (name => relation declaration) */
/** @var string $className class name */
/** @var string $modelClassName related model class name */

$modelFullClassName = $modelClassName;
if ($generator->ns !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>

namespace <?= $generator->queryNs ?>;

/**
 * This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
 *
 * @see <?= $modelFullClassName . "\n" ?>
 */
final class <?= $className ?> extends <?= '\\' . \ltrim($generator->queryBaseClass, '\\') . "\n" ?>
{
    /**
     * Gets published items.
     * @return $this
     */
    /*public function published(): self
    {
        $this->andWhere('[[status]]=1');

        return $this;
    }*/

    /**
     * Gets not deleted items.
     * @return $this
     */
    /*public function actual(): self
    {
        $this->andWhere('[[deleted]]=0');

        return $this;
    }*/

    /**
     * Gets deleted items.
     * @return $this
     */
    /*public function deleted(): self
    {
        $this->andWhere('[[deleted]]=1');

        return $this;
    }*/

    /**
     * Sorts items by position.
     * @return $this
     */
    /*public function ordered(): self
    {
        $this->orderBy('[[position]] ASC');

        return $this;
    }*/

    /**
     * Shows item by its slug parameter.
     * @param string $slug
     * @return $this
     */
    /*public function show(string $slug): self
    {
        $this->andWhere(['[[slug]]' => $slug]);

        return $this;
    }*/

    /**
     * {@inheritdoc}
     * @return <?= $modelFullClassName ?>[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return <?= $modelFullClassName ?>|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

}
