<?php

namespace Ollico\AuditLog\Tests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Ollico\AuditLog\AuditLog;
use Ollico\AuditLog\LogAuditableEvent;
use Ollico\AuditLog\Tests\Enums\TestEnum;
use Ollico\AuditLog\Tests\Models\Article;
use Ollico\AuditLog\Tests\Models\User;

class AuditLogTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->article = Article::first();

        $this->user = User::first();
    }

    /** @test */
    public function it_can_log_activity_directly()
    {
        (new AuditLog())
            ->causer($this->user)
            ->dimension($this->article)
            ->properties([
                'key' => 'value',
            ])
            ->log(TestEnum::from(TestEnum::ENUM->value));

        $activity = $this->getLastActivity();

        $this->assertEquals(TestEnum::ENUM->value, $activity->description);
        $this->assertEquals($this->article->id, $activity->subject->id);
        $this->assertInstanceOf(Article::class, $activity->subject);
        $this->assertEquals($this->article->id, $activity->causer->id);
        $this->assertInstanceOf(User::class, $activity->causer);
        $this->assertInstanceOf(Collection::class, $activity->properties);
    }

    /** @test */
    public function it_can_push_audit_to_queue()
    {
        Queue::fake();

        $this->actingAs($this->user);

        // This tests the base `audit` method also
        audit_user($this->article, TestEnum::ENUM->value, ['prop1' => 'data']);

        Queue::assertPushedOn(
            'auditlog',
            LogAuditableEvent::class,
            function (LogAuditableEvent $job) {
                return $job->dimension->id === $this->article->id
                    && $job->causer->id === $this->user->id
                    && $job->activity === TestEnum::ENUM->value
                    && $job->props['prop1'] === 'data';
            }
        );
    }
}
