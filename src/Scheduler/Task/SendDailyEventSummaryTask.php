<?php

namespace App\Scheduler\Task;

use Symfony\Component\Scheduler\Attribute\AsCronTask;
use App\Scheduler\Service\EventSummaryMailer;

#[AsCronTask('* * * * *')] 
class SendDailyEventSummaryTask
{
    public function __construct(
        private EventSummaryMailer $mailer,
    ) {}

    public function __invoke(): void
    {
        $this->mailer->sendSummaryToAllOrganizers();
    }
}
