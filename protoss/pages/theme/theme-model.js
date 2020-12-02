import {Base} from "../../utils/base.js";

class Theme extends Base{
  constructor(){
    super();
  }

  // 获取对应主题id号,下的商品数据
  getProductsData(id, callback){
    var params = {
      url : 'theme/' + id,
      sCallBack: function (data) {
          callback && callback(data);
      }
    }
    this.request(params);
  }
}
export {Theme}