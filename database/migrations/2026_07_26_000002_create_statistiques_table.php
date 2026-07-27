<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Historique des scans d'un QR. Chaque ligne = un scan.
     * Le nombre total de scans, le pays le plus fréquent, etc. se
     * calculent par requête (COUNT/GROUP BY) plutôt que d'être
     * dupliqués en colonnes sur `resources` -> une seule source de vérité.
     */
    public function up(): void
    {
        Schema::create('statistiques', function (Blueprint $table) {
            $table->id(); // BIGINT auto-increment, volume potentiellement élevé

            $table->foreignUuid('resource_id')
                ->constrained('resources')
                ->cascadeOnDelete();

            $table->timestamp('scanned_at')->useCurrent();
            $table->text('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();

            $table->timestamps();

            $table->index(['resource_id', 'scanned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistiques');
    }
};
