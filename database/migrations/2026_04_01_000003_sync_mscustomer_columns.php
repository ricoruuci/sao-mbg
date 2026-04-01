<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('mscustomer')) {
            Schema::create('mscustomer', function (Blueprint $table) {
                $table->string('customer_id', 50)->primary();
                $table->string('customer_name', 100);
                $table->string('customer_contact_person', 100)->nullable();
                $table->string('customer_city', 100)->nullable();
                $table->string('customer_phone', 30)->nullable();
                $table->string('customer_email', 100)->nullable();
                $table->string('customer_npwp', 50)->nullable();
                $table->string('customer_account_manager', 100)->nullable();
                $table->decimal('customer_limit_piutang', 18, 2)->default(0);
                $table->string('customer_address', 255)->nullable();
                $table->string('customer_address_npwp', 255)->nullable();
                $table->string('customer_note', 255)->nullable();
                $table->integer('customer_term')->default(0);
                $table->dateTime('upddate')->nullable();
                $table->string('upduser', 50)->nullable();
            });

            return;
        }

        $this->renameColumnIfExists('mscustomer', 'custid', 'customer_id');
        $this->renameColumnIfExists('mscustomer', 'custname', 'customer_name');
        $this->renameColumnIfExists('mscustomer', 'cp', 'customer_contact_person');
        $this->renameColumnIfExists('mscustomer', 'city', 'customer_city');
        $this->renameColumnIfExists('mscustomer', 'hp', 'customer_phone');
        $this->renameColumnIfExists('mscustomer', 'email', 'customer_email');
        $this->renameColumnIfExists('mscustomer', 'npwp', 'customer_npwp');
        $this->renameColumnIfExists('mscustomer', 'account_manager', 'customer_account_manager');
        $this->renameColumnIfExists('mscustomer', 'limit_piutang', 'customer_limit_piutang');
        $this->renameColumnIfExists('mscustomer', 'address', 'customer_address');
        $this->renameColumnIfExists('mscustomer', 'address_npwp', 'customer_address_npwp');
        $this->renameColumnIfExists('mscustomer', 'note', 'customer_note');
        $this->renameColumnIfExists('mscustomer', 'term', 'customer_term');

        Schema::table('mscustomer', function (Blueprint $table) {
            if (!Schema::hasColumn('mscustomer', 'customer_id')) {
                $table->string('customer_id', 50)->nullable();
            }
            if (!Schema::hasColumn('mscustomer', 'customer_name')) {
                $table->string('customer_name', 100)->nullable();
            }
            if (!Schema::hasColumn('mscustomer', 'customer_contact_person')) {
                $table->string('customer_contact_person', 100)->nullable();
            }
            if (!Schema::hasColumn('mscustomer', 'customer_city')) {
                $table->string('customer_city', 100)->nullable();
            }
            if (!Schema::hasColumn('mscustomer', 'customer_phone')) {
                $table->string('customer_phone', 30)->nullable();
            }
            if (!Schema::hasColumn('mscustomer', 'customer_email')) {
                $table->string('customer_email', 100)->nullable();
            }
            if (!Schema::hasColumn('mscustomer', 'customer_npwp')) {
                $table->string('customer_npwp', 50)->nullable();
            }
            if (!Schema::hasColumn('mscustomer', 'customer_account_manager')) {
                $table->string('customer_account_manager', 100)->nullable();
            }
            if (!Schema::hasColumn('mscustomer', 'customer_limit_piutang')) {
                $table->decimal('customer_limit_piutang', 18, 2)->default(0);
            }
            if (!Schema::hasColumn('mscustomer', 'customer_address')) {
                $table->string('customer_address', 255)->nullable();
            }
            if (!Schema::hasColumn('mscustomer', 'customer_address_npwp')) {
                $table->string('customer_address_npwp', 255)->nullable();
            }
            if (!Schema::hasColumn('mscustomer', 'customer_note')) {
                $table->string('customer_note', 255)->nullable();
            }
            if (!Schema::hasColumn('mscustomer', 'customer_term')) {
                $table->integer('customer_term')->default(0);
            }
            if (!Schema::hasColumn('mscustomer', 'upddate')) {
                $table->dateTime('upddate')->nullable();
            }
            if (!Schema::hasColumn('mscustomer', 'upduser')) {
                $table->string('upduser', 50)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('mscustomer')) {
            return;
        }

        $this->renameColumnIfExists('mscustomer', 'customer_id', 'custid');
        $this->renameColumnIfExists('mscustomer', 'customer_name', 'custname');
        $this->renameColumnIfExists('mscustomer', 'customer_contact_person', 'cp');
        $this->renameColumnIfExists('mscustomer', 'customer_city', 'city');
        $this->renameColumnIfExists('mscustomer', 'customer_phone', 'hp');
        $this->renameColumnIfExists('mscustomer', 'customer_email', 'email');
        $this->renameColumnIfExists('mscustomer', 'customer_npwp', 'npwp');
        $this->renameColumnIfExists('mscustomer', 'customer_account_manager', 'account_manager');
        $this->renameColumnIfExists('mscustomer', 'customer_limit_piutang', 'limit_piutang');
        $this->renameColumnIfExists('mscustomer', 'customer_address', 'address');
        $this->renameColumnIfExists('mscustomer', 'customer_address_npwp', 'address_npwp');
        $this->renameColumnIfExists('mscustomer', 'customer_note', 'note');
        $this->renameColumnIfExists('mscustomer', 'customer_term', 'term');
    }

    private function renameColumnIfExists(string $table, string $from, string $to): void
    {
        if (!Schema::hasColumn($table, $from) || Schema::hasColumn($table, $to)) {
            return;
        }

        DB::statement("EXEC sp_rename '{$table}.{$from}', '{$to}', 'COLUMN'");
    }
};
