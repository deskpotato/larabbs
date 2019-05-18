<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{

    use Traits\ActiveUserHelper;

    use Traits\LastActivedAtHelper;

    use HasRoles;

    use Notifiable {
        notify as protected laravelNotify;
    }


    //发送通知消息

    public function notify($instance)
    {
        if($this->id == Auth::id()){
            return;
        }
        if(method_exists($instance,'toDatabase')){
            $this->increment('notification_count');
        }

        $this->laravelNotify($instance);

    }

    //标记通知为已读

    public function markAsRead(){
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','introduction','avatar','phone'
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


    public function topics()
    {
        return $this->hasMany(Topic::class);    
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);    
    }


    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }

    //修改器
    public function setPasswordAttribute($value)
    {
        if(strlen($value) !=60){
            $value = bcrypt($value);
        }
        $this->attributes['password'] = $value;
    }

    public function setAvatarAttribute($path)
    {
        if(!starts_with($path,'http')){
            
            $path = config('app.url')."/uploads/images/avatar/$path";
        }

        $this->attributes['avatar'] = $path;
    }
}
