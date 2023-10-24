<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use App\Notifications\MyResetPassword;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'name', 'photo','email', 'password','code','status','email_verified_at','role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role(){
        return $this->belongsTo(\App\Models\Role::class);
    }

    public function authorizeRole($role){
        abort_unless($this->hasRole($role), 401); return true;
    }

    public function hasRole($role){
        if ($this->roles()->where('name', $role)->first()) {return true;}    return false;
    }

    public function photo(){
        $p=$this->photo;
        if(preg_match('/http:/',$p)){
            return $p;
            // $pos=strpos($p,'/',8);
            // $path=substr($p,$pos);
            // $filename=str_replace('/storage/','',$path);
            // if (Storage::disk('public')->exists($filename)) {
            //     return asset($path);
            // }
        }
        return asset("/images/defaultuser.png");    
    }

    public function sendPasswordResetNotification($token){
        $this->notify(new MyResetPassword($token)); 
    }
}
