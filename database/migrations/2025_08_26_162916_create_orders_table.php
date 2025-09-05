<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('total');
            $table->string('status')->default('pending');
            $table->string('stripe_payment_intent_id')->nullable()->unique(); // Stripe's PaymentIntent ID
            $table->timestamps();
        });
    }
};
