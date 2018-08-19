<?php
/**
 * Created by PhpStorm.
 * User: Poyarkov S. <webmaster.cipa at gmail dot com>
 * Date: 29.07.18
 * Time: 23:42
 */

namespace dashboard\commands;

use dashboard\models\user\AuthorRule;
use dashboard\models\user\web\User;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\db\Connection;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\rbac\ManagerInterface;

/**
 * Manage with backend users.
 *
 * @package dashboard\commands
 * @author Poyarkov S. <webmaster.cipa at gmail dot com>
 */
class UserController extends Controller
{
    /**
     * @var Connection DB instance
     */
    protected $db;
    /**
     * @var string Auto confirm (yes|no)
     */
    public $force = 'no';

    /**
     * {@inheritdoc}
     */
    public function options($actionID): array
    {
        return ArrayHelper::merge(parent::options($actionID), ['force']);
    }

    /**
     * {@inheritdoc}
     */
    public function optionAliases(): array
    {
        return ArrayHelper::merge(parent::optionAliases(), [
            'f' => 'force'
        ]);
    }

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        $this->db = User::getDb();
    }

    /**
     * Create roles and permissions.
     * @return int
     * @throws \Exception
     * @throws Exception
     */
    public function actionRbac(): int
    {
        /** @var ManagerInterface $auth */
        $auth = \Yii::$app->get('authManagerBackend');

        $auth->removeAll();

        $showData = $auth->createPermission('showData');
        $showData->description = 'Просмотр данных приложения';
        $auth->add($showData);

        $addData = $auth->createPermission('addData');
        $addData->description = 'Добавление новых данных приложения';
        $auth->add($addData);

        $updateData = $auth->createPermission('updateData');
        $updateData->description = 'Изменение данных приложения';
        $auth->add($updateData);

        $delData = $auth->createPermission('delData');
        $delData->description = 'Удаление данных приложения';
        $auth->add($delData);

        $authorRule = new AuthorRule();
        $auth->add($authorRule);

        $isOwner = $auth->createPermission('isOwner');
        $isOwner->description = 'Работа с собственными данными';
        $isOwner->ruleName = $authorRule->name;
        $auth->add($isOwner);

        $demonstration = $auth->createRole(User::ROLE_DEMO);
        $demonstration->description = 'Демо';
        $auth->add($demonstration);
        $auth->addChild($demonstration, $showData);

        $author = $auth->createRole(User::ROLE_AUTHOR);
        $author->description = 'Автор';
        $auth->add($author);
        $auth->addChild($author, $demonstration);
        $auth->addChild($author, $addData);
        $auth->addChild($author, $isOwner);

        $moderator = $auth->createRole(User::ROLE_MODER);
        $moderator->description = 'Модератор';
        $auth->add($moderator);
        $auth->addChild($moderator, $author);
        $auth->addChild($moderator, $isOwner);
        $auth->addChild($moderator, $updateData);
        $auth->addChild($moderator, $delData);

        $administrator = $auth->createRole(User::ROLE_ADMIN);
        $administrator->description = 'Администратор';
        $auth->add($administrator);
        $auth->addChild($administrator, $moderator);
        $auth->addChild($administrator, $isOwner);

        $root = $auth->createRole(User::ROLE_SUPER);
        $root->description = 'Суперпользователь';
        $auth->add($root);
        $auth->addChild($root, $administrator);
        $auth->addChild($root, $isOwner);

        // external rules
        $external_rules = \dashboard\Module::getInstance()->user_rules;
        if ($external_rules !== null) {
            try {
                $external_rules(
                    $auth,
                    compact('showData', 'addData', 'updateData', 'delData', 'isOwner'),
                    compact(User::ROLE_DEMO, User::ROLE_AUTHOR, User::ROLE_MODER, User::ROLE_ADMIN, User::ROLE_SUPER)
                );
            } catch (\Throwable $e) {
                throw new InvalidConfigException('Additional user rules is not defined properly. ' . $e->getMessage());
            }
        }

        // assign roles to all existing users
        /** @var array|User[] $users */
        $users = User::find()->all();
        foreach ($users as $user) {
            $auth->assign(
                $auth->getRole($user->role),
                $user->id
            );
        }

        $this->stdout("Done.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * Show permissions and roles.
     * @return int
     * @throws \Exception
     * @throws InvalidConfigException
     */
    public function actionAuthInfo(): int
    {
        /** @var ManagerInterface $auth */
        $auth = \Yii::$app->get('authManagerBackend');

        $permissions = $auth->getPermissions();
        $roles = $auth->getRoles();

        $rows = [];
        $data = [];

        $permissions_str = '';
        foreach ($permissions as &$permission) {
            $permissions_str .= $permission->description . ', ';
        }
        unset($permission);
        $data[0] = trim($permissions_str, ', ');

        $roles_str = '';
        foreach ($roles as &$role) {
            $roles_str .= $role->description . ', ';
        }
        unset($role);
        $data[1] = trim($roles_str, ', ');

        $rows[] = $data;
        $table = Table::widget([
            'headers' => ['permissions', 'roles'],
            'rows' => $rows
        ]);

        $this->stdout($table, Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * Create new user with username, email, password and role.
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $role Default role is "administrator". Available roles are "moderator", "administrator", "root", "demonstration".
     * @return int
     */
    public function actionCreate($username = null, $email = null, $password = null, $role = null): int
    {
        $user = new User();

        if ($username === null) {
            $promt = $this->ansiFormat('Enter username: ', Console::BOLD);
            $username = $this->prompt($promt, ['required' => true]);
        }
        if ($email === null) {
            $promt = $this->ansiFormat('Enter email: ', Console::BOLD);
            $email = $this->prompt($promt, ['required' => true]);
        }
        if ($password === null) {
            $promt = $this->ansiFormat('Enter password: ', Console::BOLD);
            $password = $this->prompt($promt, ['required' => true]);
        }
        if ($role === null) {
            $promt = $this->ansiFormat('Choose role: ', Console::BOLD);
            $role = $this->select($promt, $user->getRoles());
        }

        $u = $this->ansiFormat($username, Console::BOLD, Console::FG_YELLOW);
        $e = $this->ansiFormat($email, Console::FG_YELLOW);
        $p = $this->ansiFormat($password, Console::FG_YELLOW);
        $r = $this->ansiFormat($role, Console::FG_YELLOW);

        switch ($this->force) {
            case 'yes':
                $confirm = true;
                break;
            case 'no':
                $confirm = $this->confirm("Create new user $u with email $e and password $p as $r?");
                break;
            default:
                $confirm = false;
        }

        if (!$confirm) {
            $this->stdout("Canceled.\n", Console::FG_PURPLE);

            return ExitCode::OK;
        }

        $user->username = $username;
        $user->password = $password;
        $user->email = $email;
        $user->role = $role;
        $user->tfa = $user::TFA_DISABLED;
        $user->status = $user::STATUS_ACTIVE;

        if (!$user->save()) {
            $errors = Console::errorSummary($user);
            $this->stderr($errors, Console::FG_RED);

            return ExitCode::CANTCREAT;
        }

        $this->stdout("Done.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * Delete user by ID or username.
     * @param string|null $search_criteria ID or username
     * @return int
     * @throws \Exception
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($search_criteria): int
    {
        /** @var User|null $user */
        $user = is_numeric($search_criteria)
            ? User::findOne($search_criteria)
            : User::findOne(['username' => $search_criteria]);

        if ($user === null) {
            $this->stderr("Not found.\n", Console::FG_RED);

            return ExitCode::NOUSER;
        }

        switch ($this->force) {
            case 'yes':
                $confirm = true;
                break;
            case 'no':
                $confirm = $this->confirm("Delete user $search_criteria?");
                break;
            default:
                $confirm = false;
        }

        if (!$confirm) {
            $this->stdout("Canceled.\n", Console::FG_PURPLE);

            return ExitCode::OK;
        }

        $result = $user->delete();

        if ($result === false) {
            $this->stderr("Not deleted.\n", Console::FG_RED);

            return ExitCode::UNAVAILABLE;
        }

        $this->stdout("Done.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * Change user status by ID or username.
     * @param string|null $search_criteria ID or username
     * @return int
     * @throws InvalidArgumentException
     */
    public function actionStatus($search_criteria): int
    {
        $promt = $this->ansiFormat('Choose status: ', Console::BOLD);
        $status_list = (new User())->getList('statuses');
        $status = $this->select($promt, $status_list());

        /** @var User|null $user */
        $user = is_numeric($search_criteria)
            ? User::findOne($search_criteria)
            : User::findOne(['username' => $search_criteria]);

        if ($user === null) {
            $this->stderr("Not found.\n", Console::FG_RED);

            return ExitCode::NOUSER;
        }

        switch ($this->force) {
            case 'yes':
                $confirm = true;
                break;
            case 'no':
                $confirm = $this->confirm("Change status of user $search_criteria?");
                break;
            default:
                $confirm = false;
        }

        if (!$confirm) {
            $this->stdout("Canceled.\n", Console::FG_PURPLE);

            return ExitCode::OK;
        }

        $user->password = null;
        $user->status = $status;
        if (!$user->save()) {
            $errors = Console::errorSummary($user);
            $this->stderr($errors, Console::FG_RED);

            return ExitCode::CANTCREAT;
        }

        $this->stdout("Done.\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * Show all existing users, one by ID or one by username.
     * @param string|null $search_criteria ID or username
     * @return int
     * @throws \Exception
     */
    public function actionIndex($search_criteria = null): int
    {
        if ($search_criteria === null) {
            $model = null;
            $models = User::find()->all();
        } elseif (is_numeric($search_criteria)) {
            $model = User::findOne($search_criteria);
            $models = $model === null ? [] : [$model];
        } else {
            $model = User::findOne(['username' => $search_criteria]);
            $models = $model === null ? [] : [$model];
        }
        unset($model);

        if (empty($models)) {
            $this->stdout("Nothing to show.\n", Console::FG_PURPLE);

            return ExitCode::OK;
        }

        /**
         * @param User $user User model.
         * @return array Single row of user info in the table
         */
        function user_info(User $user): array
        {
            $status_list = $user->getList('statuses');
            $status = $status_list()[$user->status];
            $role = $user->getRoles(true, true)[$user->role];

            return [
                $user->id,
                $user->username,
                $user->email,
                $role,
                $status
            ];
        }

        $rows = [];
        foreach ($models as &$model) {
            $rows[] = user_info($model);
        }
        unset($model);

        $table = Table::widget([
            'headers' => ['id', 'name', 'email', 'role', 'status'],
            'rows' => $rows
        ]);

        $this->stdout($table, Console::FG_GREEN);

        return ExitCode::OK;
    }

}
