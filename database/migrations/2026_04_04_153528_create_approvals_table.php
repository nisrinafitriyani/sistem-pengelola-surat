<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('reference_number', 30)->unique();
            $table->date('approval_date');
            $table->string('client_pic_name', 100);
            $table->string('attachment_path', 255)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('pending'); // pending or completed
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
