<?php

namespace App\Console;

use App\Console\Commends\SeekAnswers;
use App\Console\Commends\SeekQuestions;
use App\Console\Commends\TranslateBaidu;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'seek-questions'    => SeekQuestions::class,
        'seek-answers'      => SeekAnswers::class,
        'translate-baidu'   => TranslateBaidu::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('seek-questions')->everyTenMinutes();   //根据话题爬取问题   １０分钟／1次
        $schedule->command('seek-answers')->everyTenMinutes();   //根据问题爬取回答   １０分钟／1次
        $schedule->command('translate-baidu')->everyMinute();   //翻译回答   １分钟／1次
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
