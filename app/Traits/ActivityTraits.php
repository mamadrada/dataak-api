<?php
namespace App\Traits;

use App\Activity;
use carbon\carbon;
use Auth;
trait ActivityTraits
{

    public function logCreatedActivity($logModel, $changes, $request)
    {
        $activity = activity('create')
            ->causedBy(Auth::user())
            ->performedOn($logModel)
            ->withProperties(['attributes'=>$request])
            ->log($changes);
        $lastActivity = Activity::all()->last();

        return true;
    }

    public function logUpdatedActivity($list, $before, $list_changes)
    {
        unset($list_changes['updated_at']);
        $old_keys = [];
        $old_value_array = [];
        if (empty($list_changes)) {
            $changes = 'No attribute changed';
        } else {
            if (count($before)>0) {
                foreach ($before as $key=>$original) {
                    if (array_key_exists($key, $list_changes)) {
                        $old_keys[$key]=$original;
                    }
                }
            }
            $old_value_array = $old_keys;
            $changes = 'Updated with attributes '.implode(', ', array_keys($old_keys)).' with '.implode(', ', array_values($old_keys)).' to '.implode(', ', array_values($list_changes));
        }

        $properties = [
            'attributes'=>$list_changes,
            'old' =>$old_value_array
        ];

        $activity = activity('update')
            ->causedBy(Auth::user())
            ->performedOn($list)
            ->withProperties($properties)
            ->log($changes.' made by '.Auth::user()->first_name.' '.Auth::user()->last_name);

        return true;
    }

    public function logDeletedActivity($list, $changeLogs)
    {
        $attributes = $this->unsetAttributes($list);

        $properties = [
            'attributes' => $attributes->toArray()
        ];

        $activity = activity('delete')
            ->causedBy(Auth::user())
            ->performedOn($list)
            ->withProperties($properties)
            ->log($changeLogs);

        return true;
    }

    public function logLoginDetails($user)
    {
        $updated_at = Carbon::now()->format('d/m/Y H:i:s');
        $properties = [
            'attributes' =>['name'=>$user->first_name.' '.$user->last_name,'description'=>'Login into the system at '.$updated_at]
        ];
        $changes = 'User '.$user->first_name.' '.$user->last_name.' loged in into the system';

        $activity = activity('login')
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties($properties)
            ->log($changes);

        return true;
    }

    public function unsetAttributes($model)
    {
        unset($model->created_at);
        unset($model->updated_at);
        return $model;
    }
}
