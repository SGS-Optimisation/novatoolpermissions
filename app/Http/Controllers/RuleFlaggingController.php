<?php

namespace App\Http\Controllers;

use App\Events\Rules\Flagged;
use App\Events\Rules\Unflagged;
use App\Models\Rule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RuleFlaggingController extends Controller
{

    public function flag(Request $request, Rule $rule)
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

        event(new Flagged($rule));

        \Log::debug('flagging rule ' . $rule->id);
        logger('reason: ' . $request->reason);

        return back();
    }

    public function unflag(Request $request, Rule $rule)
    {
        $rule->flagged = false;

        $metadata = $rule->metadata ?? [];
        $metadata['flag_reason'] = [];

        $rule->metadata = $metadata;
        $rule->timestamps = false;

        $rule->save();

        event(new Unflagged($rule));

        \Log::debug('unflagging rule ' . $rule->id);

        return back();
    }
}
