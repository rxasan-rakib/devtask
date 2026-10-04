<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use Illuminate\Console\Command;

class SendRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and send due email reminders and task alerts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking due reminders...');

        $processed = ReminderService::processDueReminders();
        $count = count($processed);

        $this->info("Successfully processed {$count} due reminder(s).");

        return Command::SUCCESS;
    }
}
