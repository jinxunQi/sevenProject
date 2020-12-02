// pages/theme/theme.js
import {Theme} from "./theme-model.js";
var theme = new Theme();
Page({

  /**
   * 页面的初始数据
   */
  data: {

  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var id = options.id;
    var name = options.name;
    this.data.id = id;// 绑定数据到data中，相当于成员属性
    this.data.name = name;// 绑定数据到data中，相当于成员属性
    this._loadData();
  },

  // 加载数据
  _loadData: function () {
    //加载主题id对应的商品数据
    theme.getProductsData(this.data.id, (data)=>{
      this.setData({
        'themeInfo':data
      });
    });
    
  }
})