<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_id')->constrained()->cascadeOnDelete();
            $table->string('delivery_number', 30)->unique();
            $table->date('date');
            $table->string('vehicle_type', 50)->nullable();
            $table->string('vehicle_plate', 15)->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->string('receiver_name', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('signature_image', 255)->nullable(); // Tanda tangan
            $table->string('signature_name', 100)->nullable();
            $table->string('signature_role', 100)->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_notes');
    }
};
