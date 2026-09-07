<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReorderMemberCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:reorder-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reorder member codes sequentially based on registration date (created_at)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting member code reordering...');

        // Get all members ordered by registration date ascending (oldest first)
        $members = \App\Models\Member::orderBy('created_at', 'asc')->get();
        $count = 0;

        foreach ($members as $index => $member) {
            $sequence = $index + 1;
            $newCode = 'MEM-' . str_pad($sequence, 7, '0', STR_PAD_LEFT);
            
            if ($member->member_code !== $newCode) {
                $member->member_code = $newCode;
                $member->save();
                $count++;
                $this->line("Updated member ID {$member->id} to {$newCode}");
            }
        }

        $this->info("Completed. Updated {$count} member codes.");
    }
}
