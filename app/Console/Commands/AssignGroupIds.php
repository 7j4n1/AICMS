<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;

class AssignGroupIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:assign-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign group IDs to members who do not have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for members without group assignments...');

        $membersWithoutGroups = Member::whereNull('groupId')->count();
        
        if ($membersWithoutGroups === 0) {
            $this->info('All members already have group assignments.');
            return 0;
        }

        $this->info("Found {$membersWithoutGroups} members without group assignments.");
        $this->info('Assigning group IDs...');

        $bar = $this->output->createProgressBar($membersWithoutGroups);
        $bar->start();

        Member::whereNull('groupId')->chunk(100, function ($members) use ($bar) {
            foreach ($members as $member) {
                $member->groupId = Member::calculateGroupId($member->coopId);
                $member->save();
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('Group IDs assigned successfully!');

        return 0;
    }
}
