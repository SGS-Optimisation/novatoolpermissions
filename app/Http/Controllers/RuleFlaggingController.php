<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RuleFlaggingController extends Controller
{

    public function on(Request $request, Rule $rule)
    {
        $rule->flagged = true;

        $metadata = $rule->metadata ?? [];

        if(!isset($metadata['flag_reason'])) {
            $metadata['flag_reason'] = [];
        }

        $metadata['flag_reason'][] = [
            'user' => $request->user()->name,
            'reason' => $request->reason,
            'date' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        $rule->metadata = $metadata;
        $rule->timestamps = false;

        $rule->save();

        \Log::debug('flagging rule ' . $rule->id);
        logger('reason: ' . $request->reason);

        return back();
    }
}
