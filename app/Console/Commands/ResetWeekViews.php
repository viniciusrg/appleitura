<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;

class ResetWeekViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-week-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Book::query()->update(['week_views' => 0]);
        $this->info('Week views reset successfully');
    }
}
