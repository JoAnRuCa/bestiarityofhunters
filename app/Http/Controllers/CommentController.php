<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'item_id'    => 'required|integer',
            'comentario' => 'required|string|min:3|max:1000',
            'padre'      => 'nullable|integer',
            'type'       => 'required|in:guide,build'
        ]);

        $config = [
            'guide' => [
                'model' => \App\Models\GuidesComment::class,
                'fk'    => 'guide_id',
                'guide_model' => \App\Models\Guide::class
            ],
            'build' => [
                'model' => \App\Models\BuildsComment::class,
                'fk'    => 'build_id'
            ]
        ];

        $setup = $config[$request->type];

        $comment = $setup['model']::create([
            'user_id'           => Auth::id(),
            $setup['fk']        => $request->item_id,
            'comentario'        => $request->comentario,
            'padre'             => $request->padre,
        ]);

        if ($request->ajax()) {
            $guide = ($request->type == 'guide') ? $setup['guide_model']::find($request->item_id) : null;
            
            return response()->json([
                'success' => true,
                'comment_html' => view('layouts.partials.comment', [
                    'comment' => $comment,
                    'level'   => intval($request->input('level', 0)),
                    'guide'   => $guide
                ])->render()
            ]);
        }

        return back();
    }
}