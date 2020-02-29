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

    public function invitaion()
    {
        try {
            $invitations = Invite::where('user_id', Auth::User()->id)->get();
            return response()->json([
                'success' => true,
                'data' => $invitations
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'error in get invitation'
            ], 500);
        }
    }

    public function invitePerson(Appointment $appointment, Request $request)
    {
        $this->validate($request, [
            'contact_detail' => 'required',
        ]);
        $user = User::where('email', $request->contact_detail)->orWhere('mobile', $request->contact_detail)->first();
        if (!$user) {
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

    public function setStatus(Invite $invitation, Request $request)
    {
        if ($invitation->user_id != Auth::User()->id) {
            return response()->json([
                'success' => false,
                'message' => 'this is not your invitation'
            ], 403);
        }
        DB::beginTransaction();
        try {
            $beforeUpdateValues = $invitation->toArray();
            $invitation->status = $request->status;
            $invitation->save();
            $afterUpdateValues = $invitation->getChanges();
            $this->logUpdatedActivity($invitation, $beforeUpdateValues, $afterUpdateValues);
            DB::commit();
            return response()->json([
                'success' => true,
                'data' => $invitation
                    ->toArray()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Appointment could not be updated'
            ], 500);
        }
    }

    public function inviteList(Appointment $appointment)
    {
        try {
            $invites = Invite::where('appointment_id', $appointment->id)->get();
            $statuses = getPrettyAll('invite_statuses', 'id', 'label');
            $personList = array();
            foreach ($invites as $key => $value) {
                $personList[$key]['user_id'] = $value->user_id;
                $personList[$key]['status'] = $statuses[$value->status];
            }
            return response()->json([
                'success' => true,
                'data' => $personList
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'error in get list'
            ], 500);
        }
    }
}
