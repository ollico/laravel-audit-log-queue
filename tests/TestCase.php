<?php

namespace Ollico\AuditLog\Tests;

use AddBatchUuidColumnToActivityLogTable;
use AddEventColumnToActivityLogTable;
use CreateActivityLogTable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Schema;
use Ollico\AuditLog\AuditLogServiceProvider;
use Ollico\AuditLog\Tests\Enums\TestEnum;
use Ollico\AuditLog\Tests\Models\Article;
use Ollico\AuditLog\Tests\Models\User;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\Activitylog\Models\Activity;

abstract class TestCase extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            AuditLogServiceProvider::class,
            ActivitylogServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('activitylog.database_connection', 'sqlite');
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('app.key', 'base64:' . base64_encode(
            Encrypter::generateKey($app['config']['app.cipher'])
        ));
        $app['config']->set('audit-queue.enum', TestEnum::class);
        $app['config']->set('audit-queue.queue', 'auditlog');
    }

    protected function setUpDatabase()
    {
        $this->createActivityLogTable();

        $this->createTables('articles', 'users');
        $this->seedModels(Article::class, User::class);
    }

    protected function createActivityLogTable()
    {
        include_once __DIR__ . '/../vendor/spatie/laravel-activitylog/database/migrations/create_activity_log_table.php.stub';
        include_once __DIR__ . '/../vendor/spatie/laravel-activitylog/database/migrations/add_event_column_to_activity_log_table.php.stub';
        include_once __DIR__ . '/../vendor/spatie/laravel-activitylog/database/migrations/add_batch_uuid_column_to_activity_log_table.php.stub';

        (new CreateActivityLogTable())->up();
        (new AddEventColumnToActivityLogTable())->up();
        (new AddBatchUuidColumnToActivityLogTable())->up();
    }

    protected function createTables(...$tableNames)
    {
        collect($tableNames)->each(function (string $tableName) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('text')->nullable();
                $table->timestamps();
                $table->softDeletes();

                if ($tableName === 'articles') {
                    $table->integer('user_id')->unsigned()->nullable();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->text('json')->nullable();
                    $table->decimal('price')->nullable();
                }
            });
        });
    }

    protected function seedModels(...$modelClasses)
    {
        collect($modelClasses)->each(function (string $modelClass) {
            foreach (range(1, 0) as $index) {
                $modelClass::create(['name' => "name {$index}"]);
            }
        });
    }

    public function getLastActivity(): ?Activity
    {
        return Activity::all()->last();
    }
}
