<?php

use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Models\Transfer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create($this->table(), function (Blueprint $table) {
            $enums = [
                Transfer::STATUS_EXCHANGE, // points
                Transfer::STATUS_TRANSFER,
                Transfer::STATUS_PAID, // tab
                Transfer::STATUS_REFUND,
                Transfer::STATUS_GIFT, // gift
            ];
            $table->bigIncrements('id');
            $table->morphs('from');
            $table->morphs('to');
            $table->enum('status', $enums)->default(Transfer::STATUS_TRANSFER);
            $table->enum('status_last', $enums)->nullable();
            $table->unsignedBigInteger('deposit_id');
            $table->unsignedBigInteger('withdraw_id');
            $table->decimal('discount', 64, 0)->default(0);
            $table->decimal('fee', 64, 0)->default(0);
            $table->uuid('uuid')->unique();
            $table->timestamps();

            $table->foreign('deposit_id')
                ->references('id')
                ->on($this->transactionTable())
                ->onDelete('cascade');

            $table->foreign('withdraw_id')
                ->references('id')
                ->on($this->transactionTable())
                ->onDelete('cascade');
        });
    }

    /**
     * @return string
     */
    protected function table(): string
    {
        return (new Transfer())->getTable();
    }

    /**
     * @return string
     */
    protected function transactionTable(): string
    {
        return (new Transaction())->getTable();
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::drop($this->table());
    }
}
