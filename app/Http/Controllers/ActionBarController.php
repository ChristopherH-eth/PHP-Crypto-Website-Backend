<?php

/**
 * This file contains the Action Bar Controller. It is responsible for manipulating Action Bar objects,
 * interacting with the various Action Bar database table, and handling incoming requests routed to specific
 * functions.
 * 
 * @author 0xChristopher
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActionBarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Create an action bar
     * 
     * @param Request $request
     * @param $tableName
     * @return Response
     */
    public function postActionBar(Request $request, $tableName)
    {
        $this->validate($request, [
            'name' => 'required',
            'action_bar_data' => 'required'
        ]);

        // Dynamically get the action bar class
        $modelClass = 'App\\Models\\' . ucfirst($tableName) . 'ActionBar';

        // If the class exists attempt to create the requested action bar
        if (class_exists($modelClass)) 
        {
            $actionBar = new $modelClass();
            $data = $request->json()->all();
            $actionBar->fill($data);
            $actionBar->save();

            return response()->json($actionBar, 201);
        }

        return response()->json(['error' => 'Could not create Action Bar'], 400);
    }

    /**
     * Get action bar by name
     * 
     * @param Request $request
     * @param $tableName
     * @param $barRequest
     * @return Response
     */
    public function getActionBarByName(Request $request, $tableName, $barRequest)
    {
        // Dynamically get the action bar class
        $modelClass = 'App\\Models\\' . ucfirst($tableName) . 'ActionBar';

        // If the class exists attempt to find the requested action bar
        if (class_exists($modelClass)) 
        {
            $model = new $modelClass();
            $actionBar = $model->where('name', $barRequest)->first();

            // If an action bar was found, return it; otherwise return not found
            if ($actionBar)
                return response()->json($actionBar);
            elseif (empty((array) $actionBar))
                return response()->json(['error' => 'Action Bar not found'], 404);
        }

        return response()->json(['error' => 'Action Bar not found'], 404);
    }

    /**
     * Delete an existing action bar by id
     * 
     * @param $id
     * @param $tableName
     * @return Response
     */
    public function deleteActionBar($tableName, $id)
    {
        // Dynamically get the action bar class
        $modelClass = 'App\\Models\\' . ucfirst($tableName) . 'ActionBar';

        // If the class exists attempt to find the requested action bar
        if (class_exists($modelClass)) 
        {
            $modelClass->findOrFail($id)->delete();

            return response('Action Bar deleted', 200);
        }

        return response()->json(['error' => 'Action Bar not found'], 404);
    }
}