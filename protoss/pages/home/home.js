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

  // callBack: function(res){
  //   console.log(res);//上面使用闭包来代替回调
  // },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  }
})