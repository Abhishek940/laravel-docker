<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class leaveController extends Controller
{
    public function Addleave(Request $request)
    {
        $request->validate([
            'leave_name'=>'required|max:255|unique:leave_types',
            'total_days'=>'required|numeric',
            'description'=>'required|max:255',
            'status'=>'required|boolean'
        ],
        [
           'leave_name.required'=>'leave name is required.',
           'leave_name.unique' => 'Leave name already exist.',
       ]);
         

        DB::table('leave_types')->insert([
            'leave_name'=>$request->leave_name,
            'total_days'=>$request->total_days,
            'description'=>$request->description,
            'status'=>$request->status,
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);

         return response()->json([
            'status'=>true,
            'message'=>'leave Added Successfully'
         ]);

    }

    public function getleaves(){

        $leaves =DB::table('leave_types')->get();
        return response()->json([
            'success'=>true,
            'data'=>$leaves,
        ]);
    }

    public function getleaveById($id){

        $leave =DB::table('leave_types')->where('id',$id)->first();
        if(!$leave){
            return response()->json([
                'status'=>false,
                'message'=>'leave not found'
            ],404);
        }else{
            return response()->json([
                'success'=>true,
                'data'=>$leave
            ]);

        }
    }

    public function updateleave(Request $request,$id)
    {
      $request->validate([
            'leave_name' => 'sometimes|max:255|unique:leave_types,leave_name,' . $id,
            'total_days' => 'sometimes|numeric',
            'description' => 'sometimes|max:255',
            'status' => 'sometimes|numeric',
        ]);
/* 
       $updated= DB::table('leave_types')->where('id',$id)->update([
            'leave_name'=>$request->leave_name,
            'total_days'=>$request->total_days,
            'description'=>$request->description,
            'status'=>$request->status,
        ]); */

         $updated = DB::table('leave_types')->where('id', $id)
            ->update($request->only([
            'leave_name',
            'total_days',
            'description',
            'status'
        ]));

        if(!$updated){
            return response()->json([
                'status'=>false,
                'messgae'=>'leave not founf'
            ],404);
        }

       return response()->json([
          'status'=>true,
          'message'=>'leave updated successfully',
       ]);

    }

      public function deleteleave($id){

        $leave = DB::table('leave_types')->where('id',$id)->delete();

        if(!$leave){
            return response()->json([
                'status'=>false,
                'message'=>'leave not found',
            ],404);

        }

        return response()->json([
            'status'=>true,
            'message'=>'leave deleted successfully'
        ]);
        
    }

}
