// 基础类
import {Config} from '../utils/config.js'
class Base {
    // 构造方法
    constructor(){
        this.baseRequestUrl = Config.restUrl;
    }

    // 封装发送网络请求的方法
    request(params){
        var url = this.baseRequestUrl + params.url;

        if(!params.type){
            params.type = 'GET';
        }
        wx.request({
            url: url,
            data: params.data,
            method: params.type, 
            header: {
                'content-type':'application/json',
                'token':wx.getStorageSync('token')
            },
            success: function(res){
                if (params.sCallBack) {
                    // params.sCallBack(res);
                    console.log(res);
                    params.sCallBack(res.data);
                }
                //params.sCallBack&&params.sCallBack(res);// 上面的if简写形式
            },
            fail: function(err) {
                console.log(err);
            }
        })
    }

    // 获取元素上绑定的值
    getDataSet(event, key){
        return event.currentTarget.dataset[key];
    }
}
export {Base}