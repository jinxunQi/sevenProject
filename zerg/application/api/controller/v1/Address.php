<?php
namespace app\api\controller\v1;
use app\api\controller\BaseController;
use app\api\model\User;
use app\api\model\UserAddress;
use app\api\validate\AddressValidate;
use app\api\service\Token as TokenService;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\TokenException;
use app\lib\exception\UserException;

/**
 * 地址控制器
 * Class UserAddress
 * @package app\api\controller\v1
 */
class Address extends BaseController
{
    /**
     * 前置操作数组
     * @var array
     */
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'createAndUpdateAddress'], //添加|更新地址需要验证用户权限
    ];

    /**
     * 从token中检测基础权限 scope
     * @return bool
     * @throws
     */
    public function checkPrimaryScope()
    {
        $scope = Tokenservice::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope >= ScopeEnum::User) {
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }
    }

    /**
     * 添加|更新用户地址
     * @return mixed
     * @throws
     */
    public function createAndUpdateAddress()
    {
        $validate = new AddressValidate();
        $validate->goCheck();

        $uid = TokenService::getCurrentUid();
        $user = User::get($uid);
        if (!$user) {
            throw new UserException();
        }

        //获取用户从客户端提交来的地址信息 经过验证层过滤字段
        $data = $validate->getDataByRule(input('post.'));
        //根据用户地址信息是否存在，从而判断是添加地址还是更新地址
        $userAddress = $user->address;
        if (!$userAddress) {
            //不存在用户地址，创建之
            $user->address()->save($data);
        }else{
            //更新数据 注意和添加的区别
            $user->address->save($data);
            //UserAddress::where('user_id', $uid)->update($data);
        }

        return new SuccessMessage();
    }
}