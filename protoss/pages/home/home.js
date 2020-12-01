// pages/home/home.js
import {Home} from 'home-model.js';
var home = new Home();//实例化类
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
    this._loadData();
  },

  _loadData: function (){
    var id = 1;
    var data = home.getBannerData(id, (res)=>{
      // console.log(res);
      //数据绑定
      this.setData({
        'bannerArr':res
      });
    });

    //加载精品主题数据
    home.getThemeData((data)=>{
      this.setData({
        'themeArr':data
      });
    });
    
    //加载最近商品数据
    home.getRecentProductsData((data)=>{
      this.setData({
        'productsArr':data
      });
    });
  },

   // 跳转商品详情
   onProductsItemTap: function (event) {
    var id = null;
    wx.navigateTo({
      url: '../product/product?id=' + id,
    })
  }

  // callBack: function(res){
  //   console.log(res);//上面使用闭包来代替回调
  // },
})