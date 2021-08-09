<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRuleTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('rule_team', function (Blueprint $table) {
            $table->foreignId('rule_id');
            $table->foreignId('team_id');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        DB::table('audits')
            ->where('auditable_type', 'App\Models\Rule')
            ->get()
            ->each(function ($row) {
                logger('processing auditable id '.$row->auditable_id);
                $user = \App\Models\User::find($row->user_id);
                $rule = \App\Models\Rule::find($row->auditable_id);

                if (!$rule) {
                    return;
                }

                $team = $user->allTeams()->firstWhere('client_account_id', $rule->client_account_id);

                if ($team) {
                    $rule->teams()->syncWithoutDetaching($team->id);
                }
            });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rule_team');
    }
}
