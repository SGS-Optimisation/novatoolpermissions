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
        $rule->recordFlagReason($request->user()->name, $request->reason);

        event(new Flagged($rule));

        \Log::debug('flagging rule ' . $rule->id);
        logger('reason: ' . $request->reason);

        return back();
    }

    public function unflag(Request $request, Rule $rule)
    {
        $rule->unflag();

        event(new Unflagged($rule));

        \Log::debug('unflagging rule ' . $rule->id);

        return back();
    }
}
