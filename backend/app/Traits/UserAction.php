<?php 
namespace App\Traits;
use Auth;

trait UserAction
{
    public static function bootUserAction(): void
    {
        static::creating(function ($model) {
           $user = Auth::user();
           if ($user) {
               $model->created_by = $user->id;
           }
       });
       static::updating(function($model)
       {
           $user = Auth::user();
           if ($user) {
               $model->updated_by = $user->id;
           }
       });

       static::deleting(function($model)
       {
           $user = Auth::user();
           if ($user) {
               $model->deleted_by = $user->id;
               $model->save();
           }
       });

    //    static::deleting(function($model){

    //     $user = Auth::user();
    //     $model->deleted_by = $user->id;

    //    });
   }
}