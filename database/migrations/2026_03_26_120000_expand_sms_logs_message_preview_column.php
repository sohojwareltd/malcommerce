<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE sms_logs MODIFY message_preview TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE sms_logs MODIFY message_preview VARCHAR(200) NULL');
    }
};
