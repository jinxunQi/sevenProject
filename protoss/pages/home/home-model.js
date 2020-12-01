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

    /** 首页精品主题 */
    getThemeData(callback){
        var params = {
            url : 'theme?ids=1,2,3',
            sCallBack: function (data) {
                callback && callback(data);
            }
        }
        this.request(params);
    }

    /** 获取最近新品 */
    getProductsData(callback){
        var params = {
            url : 'product/recent',
            sCallBack: function (data) {
                callback && callback(data);
            }
        }
        this.request(params);
    }
}
export {Home}