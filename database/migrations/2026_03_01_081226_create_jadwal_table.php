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
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id');
            $table->foreign('kelas_id')->references('id')->on('kelas')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('mapel_id');
            $table->foreign('mapel_id')->references('id')->on('mapel')
                ->onDelete('cascade')->onUpdate('cascade'); 
            $table->foreignId('guru_id');
            $table->foreign('guru_id')->references('id')->on('guru')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);
            $table->string('jam_pelajaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
