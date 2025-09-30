<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    
    public function groupEvent(){
        return view('auth.user.paper.register');
    }
    public function externalEvent(){
        return view('auth.user.paper.external_event');
    }
    public function externalEventStore(Request $request){
        try {
            DB::beginTransaction();
            DB::commit();
            return redirect()->route('auth.');
        } catch (\Exception $e) {
            //throw $th;

        }
    }
}
