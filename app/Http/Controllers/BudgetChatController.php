<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
#[Middleware('verified')]
class BudgetChatController extends Controller
{
    public function store(Request $request, Budget $budget)
    {
        $messages = $request->input('messages', []);
        $lastMessages = collect($messages)->last();

        $prompt = collect(data_get($lastMessages, 'parts', []))
            ->where('type', 'text')
            ->pluck('text')
            ->implode(' ')
            ?:data_get($lastMessages, 'content', '');

        dd('$prompt');
    }
}
