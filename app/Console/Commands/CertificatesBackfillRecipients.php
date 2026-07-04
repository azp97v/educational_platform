<?php

namespace App\Console\Commands;

use App\Models\Certificates\CertificateStudent;
use App\Models\User;
use Illuminate\Console\Command;

class CertificatesBackfillRecipients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:backfill-recipients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'يربط سجلات certificate_students الحالية بحسابات الطلاب الحقيقية عبر مطابقة البريد';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $linked = 0;
        $total = 0;

        CertificateStudent::whereNull('recipient_user_id')
            ->orderBy('id')
            ->chunk(200, function ($chunk) use (&$linked, &$total) {
                foreach ($chunk as $student) {
                    $total++;
                    $userId = User::whereRaw('LOWER(email) = ?', [strtolower($student->email)])->value('id');
                    if ($userId) {
                        $student->update(['recipient_user_id' => $userId]);
                        $linked++;
                    }
                }
            });

        $this->info("تمت معالجة {$total} سجل، تم ربط {$linked} منها بحساب طالب حقيقي.");
    }
}
