<?php


namespace App\Operations\ClientAccounts;


use App\Models\ClientAccount;
use Illuminate\Support\Str;

class SimplifyAliasesForSearch
{

    /**
     * SimplifyAliasesForSearch constructor.
     */
    public static function handle(ClientAccount $clientAccount, $as_str = false)
    {
        $name = $clientAccount->name;
        $list = preg_split('/\r\n|[\r\n]/', $clientAccount->alias);

        $kept_aliases = [trim($name)];

        foreach($list as $entry) {
            if(!Str::contains(trim($entry), $kept_aliases)) {
                $kept_aliases[] = trim($entry);
            }
        }

        return $as_str ? implode(',', $kept_aliases) : $kept_aliases;
    }
}
