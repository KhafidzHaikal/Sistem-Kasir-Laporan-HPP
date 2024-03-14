<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TambahForeignKeyToPenjualan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('penjualan', 'id_member')) {
            Schema::table('penjualan', function (Blueprint $table) {
                $table->unsignedInteger('id_member')->after('id_penjualan'); // Add the id_member column after the id_penjualan column

                $table->foreign('id_member')
                    ->references('id_member')
                    ->on('member')
                    ->onUpdate('restrict')
                    ->onDelete('restrict');
            });
        }

        if (!Schema::hasColumn('penjualan', 'id_user')) {
            Schema::table('penjualan', function (Blueprint $table) {
                $table->unsignedInteger('id_user')->after('id_member'); // Add the id_user column after the id_member column

                $table->foreign('id_user')
                    ->references('id_user')
                    ->on('users')
                    ->onUpdate('restrict')
                    ->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropForeign('penjualan_id_user_foreign');
            $table->dropColumn('id_user');

            $table->dropForeign('penjualan_id_member_foreign');
            $table->dropColumn('id_member');
        });
    }
}
