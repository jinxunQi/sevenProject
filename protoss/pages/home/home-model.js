import {Base} from '../../utils/base.js';
class Home extends Base{
    constructor(){
        super();
    }
    getBannerData(id, callBack){

        var params = {
            url : 'banner/' + id,
            sCallBack:function (res) {
                callBack && callBack(res.items);
            }
        }
        this.request(params);
        // wx.request({
        //     url: 'http://zerg.aaa/api/v1/banner/' + id,
        //     method: 'GET', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
        //     success: function(res){
        //         // success
        //         console.log(res);
        //         return res;
        //     }
        // })
    }
}
export {Home}