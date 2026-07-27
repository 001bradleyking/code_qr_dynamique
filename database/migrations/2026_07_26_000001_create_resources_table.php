<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table unique fusionnant l'ancien couple qr_codes + resources.
     * Un "resource" = un code QR dynamique + la donnée qu'il pointe.
     * C'est ce couple (short_code fixe / data modifiable) qui fait
     * tout le principe du "QR dynamique".
     */
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('short_code')->unique(); // le token stable encodé dans le QR
            $table->string('title')->nullable();

            $table->enum('type', [
                'WEBSITE', 'IMAGE', 'VIDEO', 'PDF', 'VCARD', 'WIFI', 'MAPS',
            ]);

            $table->json('data'); // contenu spécifique au type, modifiable sans réimprimer le QR

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
