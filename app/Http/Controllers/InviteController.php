<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Invite;
use App\Traits\ActivityTraits;
use Illuminate\Http\Request;
use App\User;
use DB;
use Auth;

class InviteController extends Controller
{
    use ActivityTraits;

    public function invite(Appointment $appointment,Request $request)
    {
        $this->validate($request, [
            'contact_detail' => 'required',
        ]);
        $user = User::where('email',$request->contact_detail)->orWhere('mobile',$request->contact_detail)->first();
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 400);
        }
        DB::beginTransaction();
        try {
            $invite = new Invite();
            $invite->appointment_id = $appointment->id;
            $invite->user_id = $user->id;
            $invite->status = config('constant.INVITE_STATUS.NEW');
            if ($invite->save()) {
                $this->logCreatedActivity($invite, 'save new invite', $invite->toArray());
                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $invite->toArray()
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'INVITE save error: ' . $e->getMessage()
            ], 500);
        }
    }
}
