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
        Schema::create('apple_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transactionId', 250);
            $table->unsignedBigInteger('user_id');
            $table->string('inAppOwnershipType', 250)->nullable();
            $table->string('subscriptionGroupIdentifier', 250)->nullable();
            $table->string('type', 250)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apple_transactions');
    }
};
