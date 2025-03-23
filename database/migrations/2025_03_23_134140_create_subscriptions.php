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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('provider');
            $table->string('product_id');
            $table->string('purchase_token');
            $table->string('status');
            $table->timestamp('expires_at')->nullable();
            $table->boolean('auto_renewing')->default(false);
            $table->integer('payment_state')->nullable();
            $table->integer('cancel_reason')->nullable();
            $table->text('provider_data')->nullable();
            $table->timestamps();

            // Índices para pesquisa rápida
            $table->index(['provider', 'purchase_token']);
            $table->index(['user_id', 'status']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
