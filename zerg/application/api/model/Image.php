<?php
namespace app\api\model;

class Image extends BaseModel
{
    protected $hidden = ['delete_time','id','from'];
}