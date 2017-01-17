console.log('hello pulse')
var app = getApp()
Page({
  onload: function () {
    console.log('pulse onload')
    app.getUserInfo(function (userInfo) {
      console.log(userInfo)
    })
  }
})
