<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Traits\ActivityTraits;
use Illuminate\Http\Request;
use DB;

class AppointmentController extends Controller
{
    use ActivityTraits;

    public function index()
    {
        $appointments = auth()->user()->appointments;
        return response()->json([
            'success' => true,
            'data' => $appointments
        ], 200);
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'time' => 'required',
            'active' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $appointment = new Appointment();
            $appointment->date = $request->date;
            $appointment->time = $request->time;
            $appointment->active = $request->active;
            if (auth()->user()->appointments()->save($appointment)) {

                $this->logCreatedActivity($appointment, 'save new Appointment', $appointment->toArray());
                DB::commit();
                return response()->json([
                    'success' => true,
                    'data' => $appointment->toArray()
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Appointment save error: ' . $e->getMessage()
            ], 500);
        }

    }

    public function update(Request $request, $id)
    {
        $appointment = auth()->user()->appointments()->find($id);
        $beforeUpdateValues = $appointment->toArray();

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment  not found'
            ], 400);
        }
        DB::beginTransaction();
        try {
            $updated = $appointment->fill($request->all())->save();
            $afterUpdateValues = $appointment->getChanges();
            $this->logUpdatedActivity($appointment, $beforeUpdateValues, $afterUpdateValues);
            DB::commit();
            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Appointment could not be updated'
            ], 500);
        }

    }
    public function destroy($id)
    {
        $appointment = auth()->user()->appointments()->find($id);
        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found'
            ], 400);
        }
        $this->logDeletedActivity($appointment, 'delete Appointment model ');
        if ($appointment->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product could not be deleted'
            ], 500);
        }
    }
}
