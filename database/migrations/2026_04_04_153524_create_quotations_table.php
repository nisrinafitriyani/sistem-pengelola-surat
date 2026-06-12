<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference_number', 30)->unique();
            $table->date('date');
            $table->string('project_name', 150);
            $table->string('project_subname', 150)->nullable();
            $table->string('service_type', 50);
            $table->string('work_category', 50);
            $table->text('subject_description');
            $table->string('type', 20)->default('po'); // po or wo
            $table->string('status', 30)->default('draft'); // draft, approve, reject
            $table->json('items'); 
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('signature_name', 100);
            $table->string('signature_role', 100);
            $table->string('signature_path', 255)->nullable();
            $table->string('stamp_path', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
