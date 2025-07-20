<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('actos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tipo_acto_id')->constrained('tipo_actos')->onDelete('cascade');
            $table->foreignId('eje_tematico_id')->constrained('eje_tematicos')->onDelete('cascade');
            $table->integer('number');
            $table->integer('year');
            $table->unique(['tipo_acto_id', 'number', 'year']);
            $table->string('archivo_url')->nullable();
            $table->timestamp('fecha')->useCurrent();
            $table->text('titulo')->nullable();
            $table->text('observacion')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->enum('state', ['pendiente', 'aprobado', 'anulado'])->default('pendiente');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actos', function (Blueprint $table) {
            Schema::dropIfExists('actos');
        });
    }
};
