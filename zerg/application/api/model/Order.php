<?php
namespace app\api\model;

class Order extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'user_id'];

}
