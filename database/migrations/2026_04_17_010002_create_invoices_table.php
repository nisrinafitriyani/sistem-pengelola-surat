<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number', 30)->unique();
            $table->date('date');
            $table->string('reff_po_number', 30);
            $table->decimal('contract_sum', 15, 2)->default(0);
            $table->string('bank_name', 50)->nullable();
            $table->string('bank_branch', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_name', 100)->nullable();
            $table->string('signature_name', 100)->nullable();
            $table->string('signature_role', 100)->nullable();
            $table->string('signature_path', 255)->nullable();
            $table->string('stamp_path', 255)->nullable();
            $table->text('notes')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
