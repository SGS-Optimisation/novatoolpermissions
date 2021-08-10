<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRuleUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('rule_user', function (Blueprint $table) {
            $table->foreignId('rule_id');
            $table->foreignId('user_id');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();

        DB::table('audits')->where('auditable_type', 'App\Models\Rule')
            ->get()
            ->each(function ($row) {
                $user = \App\Models\User::find($row->user_id);
                $user->rules()->syncWithoutDetaching($row->auditable_id);
            });

        /**
         * Remove user if they are external to the client team (e.g admins)
         * @var \App\Models\User $user
         */
        foreach (\App\Models\User::all() as $user) {
            /** @var \App\Models\Rule $rule */
            foreach ($user->rules as $rule) {
                if (!$user->belongsToOneOfClientTeams($rule->clientAccount)) {
                    $rule->users()->detach($user->id);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rule_user');
    }
}
