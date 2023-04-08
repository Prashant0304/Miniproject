<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dboy extends Model
{
    public function addusers($data,$Id='')
    {
              if(empty($id))
              {
               $getId = DB::table('delivery_user')->insertgetId($data);
               $id = $getId;
              }
              else
              {
                  $getId = DB::table('delivery_user')->where('user_id',$Id)->update($data);
              }
   
     }
      public function add_details($data,$Order_no='')
     {
               if(empty($id))
               {
                $getId = DB::table('delivery')->insertgetId($data);
                $id = $getId;
               }
               else
               {
                   $getId = DB::table('delivery')->where('order_no',$Order_no)->update($data);
               }
     }
    }
