<?php
namespace Eardish\Cron;

use Cron\Job\ShellJob;
use Cron\Schedule\CrontabSchedule;
use Cron\Resolver\ArrayResolver;
use Cron\Cron;
use Cron\Executor\Executor;

class CronManager
{

// *    *    *    *    *    *
// -    -    -    -    -    -
// |    |    |    |    |    |
// |    |    |    |    |    + year [optional]
// |    |    |    |    +----- day of week (0 - 7) (Sunday=0 or 7)
// |    |    |    +---------- month (1 - 12)
// |    |    +--------------- day of month (1 - 31)
// |    +-------------------- hour (0 - 23)
// +------------------------- min (0 - 59)

    public function __construct()
    {
        $job1 = new ShellJob();
        $job1->setCommand('php /ProcessQueue.php');
        $job1->setSchedule(new CrontabSchedule('10 * * * * *')); //check every 10 minutes


        $resolver = new ArrayResolver();
        $resolver->addJob($job1);

        $cron = new Cron();
        $cron->setExecutor(new Executor());
        $cron->setResolver($resolver);

        $cron->run();
    }
}
