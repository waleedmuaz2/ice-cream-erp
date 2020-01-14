<?php

namespace App\Repositories;

class Common
{
    public static function Data($data){
        if(sizeof($data) > 0){
            return response()->json(['data' => $data]);
        }
        return false;
    }
    
    public static function Message($message_for , $message_type = 0){
        $alert_type = "";
        if($message_type == 0){
            $message = "Not Found !";
            $alert_type = "error";
        }
        else if($message_type == 1){
            $message = "Created";
            $alert_type = "success";
        }
        else if($message_type == 2){
            $message = "Updated";
            $alert_type = "success";
        }
        else if($message_type == 3){
            $message = "Deleted";
            $alert_type = "success";
        }
        else if($message_type == 4){
            $message = "Approved";
            $alert_type = "success";
        }
        else if($message_type == 5){
            $message = "Updation not allowed";
            $alert_type = "error";
        }
        else if($message_type == 6){
            $message = "Logged";
            $alert_type = "success";
        }
        else if($message_type == 7){
            $message = "Logging Failed";
            $alert_type = "error";
        }
        return redirect()->back()->with($alert_type , $message_for. " " .$message);
    }
}