<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add performance indexes to the messages table so conversation history,
     * unread counts, and ordered fetches stay fast at scale.
     */
    public function up(): void
    {
        $existing = $this->existingIndexNames();

        Schema::table('messages', function (Blueprint $table) use ($existing) {
            // Conversation lookups: WHERE sender_id = ? AND recipient_id = ? ORDER BY created_at
            if (!in_array('messages_conversation_index', $existing, true)) {
                $table->index(['sender_id', 'recipient_id', 'created_at'], 'messages_conversation_index');
            }

            // Reverse direction of the same conversation (between() uses both orders).
            if (!in_array('messages_conversation_reverse_index', $existing, true)) {
                $table->index(['recipient_id', 'sender_id', 'created_at'], 'messages_conversation_reverse_index');
            }

            // Unread badge counts: WHERE recipient_id = ? AND read_at IS NULL
            if (!in_array('messages_recipient_read_index', $existing, true)) {
                $table->index(['recipient_id', 'read_at'], 'messages_recipient_read_index');
            }

            // Global ordering / cleanup scans.
            if (!in_array('messages_created_at_index', $existing, true)) {
                $table->index('created_at', 'messages_created_at_index');
            }
        });
    }

    public function down(): void
    {
        $existing = $this->existingIndexNames();

        Schema::table('messages', function (Blueprint $table) use ($existing) {
            foreach ([
                'messages_conversation_index',
                'messages_conversation_reverse_index',
                'messages_recipient_read_index',
                'messages_created_at_index',
            ] as $name) {
                if (in_array($name, $existing, true)) {
                    $table->dropIndex($name);
                }
            }
        });
    }

    /**
     * Return the list of existing index names on the messages table.
     */
    protected function existingIndexNames(): array
    {
        try {
            $rows = DB::select('SHOW INDEX FROM messages');
            return collect($rows)->pluck('Key_name')->unique()->values()->all();
        } catch (\Throwable $e) {
            return []; // SQLite — assume none and attempt creation
        }
    }
};
