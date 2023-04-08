<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class queryController extends Controller
{
    public function query(Request $req){
          $FBS=$req->input('FBS');
          $HBA1C=$req->input('HBA1C');
          $PPBS=$req->input('PPBS');
          $RBS=$req->input('RBS');
          $Insulin=$req->input('Insulin');
          $tablets=$req->input('tablets');
          $val=\DB::table('query')->insert(array(
          'FBS'=>$FBS,
          'HBA1C'=>$HBA1C,
          'PPBS'=>$PPBS,
          'RBS'=>$RBS,
          'Insulin'=>$Insulin,
          'tablets'=>$tablets
          ));
            if(($FBS<100)&&($HBA1C<6.5)&&(($PPBS<200)||($RBS<200)))
                     {
                         return "It is BT1";
                     }
            else if(($FBS<=150)&&($HBA1C<=7.5)&&(($PPBS<=300)||($RBS<=300)))
                     {
                         return "It is BT2";;
                     }
            else if(($FBS<=200)&&($HBA1C<=8.5)&&(($PPBS<=400)||($RBS<=400)))
            {
                return "It is BT3";
            }
            else if(($FBS<=300)&&($HBA1C<=9)&&(($PPBS<=500)||($RBS<=500)))
            {
                return "It is BT4";
            }         
            else if(($FBS>300)&&($HBA1C>9)&&($PPBS>500)||($RBS>500))
            {
                return "It is BT5";
            }
            else {
                if($PPBS!='empty')
                {
                    if($PPBS<200)
                    {
                        return "It is BT1";
                    }
                    else if($PPBS>=201 && $PPBS<=300)
                    {
                        return "It is BT2";
                    }
                    else if($PPBS>=301 && $PPBS<=400)
                    {
                        return "It is BT3";
                    }
                    else if($PPBS>=401 && $PPBS<=500)
                    {
                        return "It is BT4";
                    }
                    else 
                    {
                        return "It is BT5";
                    }
                }
                else if($PPBS=='empty'&&$RBS!='empty')
                {
                    if($RBS<200)
                    {
                       return "It is BT1"; 
                    }
                    else if($RBS>=201 && $RBS<=300)
                    {
                        return "It is BT2";
                    }
                    else if($RBS>=301 && $RBS<=400)
                    {
                        return "It is BT3";
                    }
                    else if($RBS<=401 && $RBS<=500)
                    {
                        return "It is BT4";
                    }
                    else 
                    {
                        return "It is BT5";
                    }
                }
                else if($PPBS=='empty'&&$RBS=='empty'&&$HBA1C!='empty')
                {
                    if($HBA1C<6.5)
                        {
                        return "It is BT1";
                        }
                    else if($HBA1C>=6.5&&$HBA1C<=7.5)
                        {
                            return "It is BT2";
                        }
                    else if($HBA1C>=7.6&&$HBA1C<=8.5)
                        {
                            return "It is BT3";
                        }
                    else if($HBA1C>=8.6&&$HBA1C<=9)
                    {
                        return "It is BT4";;
                    }
                    else{
                        return "It is BT5";
                    }
                }
                else if($PPBS=='empty'&&$RBS=='empty'&&$HBA1C=='empty'&&$FBS!='empty')
                {
                        if($FBS<100)
                        {
                            return "It is BT1";
                        }
                        else if($FBS>=101&&$FBS<=150)
                        {
                            return "It is BT2";
                        }
                        else if($FBS>=151&&$FBS<=200)
                        {
                            return "It is BT3";
                        }
                        else if($FBS>=201&&$FBS<=300)
                        {
                            return "It is BT4";
                        }
                        else{
                            return "It is BT5";
                        }
                }

            }

   }
}
