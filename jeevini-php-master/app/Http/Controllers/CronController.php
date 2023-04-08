<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\GroupRank;
use App\Traits\Highdisease;

class CronController extends Controller
{
    use GroupRank, highDisease;

    public function callGroup(){
         $this->groupRank();
         $this->highDisease();
         

    }

   
}
