<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 27.07.18
 * Time: 1:38
 */

namespace dashboard\models\user\web;

use dashboard\models\user\UserRecord;
use dashboard\traits\Model;
use imagetool\components\Image;
use Intervention\Image\AbstractFont;
use Intervention\Image\ImageManager;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * Class User.
 *
 * @package dashboard\models\user\web
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class User extends UserRecord
{
    use Model;

    /**
     * @var null|UploadedFile
     */
    public $avatar_file;
    /**
     * @var array
     */
    protected $statuses;
    /**
     * @var array
     */
    protected $roles;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->statuses = [
            self::STATUS_ACTIVE => \Yii::t('dashboard', 'aktiven'),
            self::STATUS_NOT_ACTIVE => \Yii::t('dashboard', 'neaktiven')
        ];

        $this->roles = [
            self::ROLE_DEMO => \Yii::t('dashboard', 'demo'),
            self::ROLE_AUTHOR => \Yii::t('dashboard', 'avtor'),
            self::ROLE_MODER => \Yii::t('dashboard', 'moderator'),
            self::ROLE_ADMIN => \Yii::t('dashboard', 'administrator'),
            self::ROLE_SUPER => \Yii::t('dashboard', 'superpolzovatel')
        ];

        $module = \dashboard\Module::getInstance();
        if ($module !== null && ArrayHelper::isAssociative($module->user_roles)) {
            $this->roles = ArrayHelper::merge($module->user_roles, $this->roles);
        }
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        $rules = [
            [['avatar_file'], 'image', 'mimeTypes' => ['image/*']]
        ];

        return ArrayHelper::merge(parent::rules(), $rules);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'avatar' => \Yii::t('dashboard', 'avatar'),
            'avatar_file' => \Yii::t('dashboard', 'avatar'),
            'username' => \Yii::t('dashboard', 'imya polzovatelya'),
            'email' => 'Email',
            'password' => \Yii::t('dashboard', 'parol'),
            'role' => \Yii::t('dashboard', 'rol'),
            'ip' => 'Ip',
            'access_token' => 'API ID',
            'note' => \Yii::t('dashboard', 'zapisi'),
            'created_at' => \Yii::t('dashboard', 'vremya sozdaniya'),
            'updated_at' => \Yii::t('dashboard', 'vremya obnovleniya'),
            'last_access' => \Yii::t('dashboard', 'posledniy vhod'),
            'status' => \Yii::t('dashboard', 'status'),
            'tfa' => \Yii::t('dashboard', 'vkl 2fa')
        ];
    }

    /**
     * Get list of available user roles.
     * @param bool $associative Is array associative
     * @param bool $all Show superadmin
     * @return array
     */
    public function getRoles(bool $associative = true, bool $all = false): array
    {
        $list = $associative ? $this->roles : array_keys($this->roles);
        if (!$all) {
            ArrayHelper::remove($list, self::ROLE_SUPER);
        }

        return $list;
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            // save avatar
            $image = UploadedFile::getInstance($this, 'avatar_file');
            $avatar_width = 128;
            $avatar_height = 128;
            // save uploaded as new
            if ($image !== null) {
                $imagetool = new Image($image->tempName, [
                    'quality' => 75
                ]);
                $imagetool->resizeProportional($avatar_width, $avatar_height);
                $this->avatar = $imagetool->encode(Image::FORMAT_DATA_URI);
            }
            // create avatar
            if ($insert && $image === null) {
                $image_manager = new ImageManager(['driver' => 'imagick']);
                $str = mb_strtoupper(mb_substr($this->username, 0, 2));
                $this->avatar = (string) $image_manager
                    ->canvas($avatar_width, $avatar_height, '#666666')
                    ->text($str, 35, 80, function (AbstractFont $font) {
                        $font->file(\Yii::getAlias(Image::FONT_FILE));
                        $font->size(56);
                        $font->color('#ffffff');
//                        $font->align('center');
//                        $font->valign('middle');
                    })
                    ->encode(Image::FORMAT_DATA_URI);
            }

            return true;
        }

        return false;
    }

}
