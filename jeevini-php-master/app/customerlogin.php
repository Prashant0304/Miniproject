<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Foundation\Auth\adminlogin as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\CustomerController;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
//use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Auth\Authenticatable;

use Tymon\JWTAuth\Contracts\JWTSubject;

class customerlogin extends Model implements JWTSubject ,AuthenticatableContract//, AuthorizableContract
{
    //use HasApiTokens;//, HasFactory;
    use Authenticatable;
    // Authorizable;
    //public $table="admin";
    protected $guard = 'customerlogin';

    protected $table='diatitian_customer_care';
    protected $fillable = [
        'Email_id',
        'password',
    ];

    

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
