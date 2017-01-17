App({
  onLaunch: function () {
    wx.login({
      success: function (res1) {
        wx.getUserInfo({
          success: function (res) {
            console.log('userInfo:')
            console.log(res1)
            console.log(res)
          }
        })
      }
    })
  // console.log('获取用户信息')
  // this.getUserInfo(function (userInfo) {
  // console.log(userInfo)
  // })
  },
  getUserInfo: function (cb) {
    var that = this
    if (this.globalData.userInfo) {
      console.log('exist info')
      typeof cb === 'function' && cb(this.globalData.userInfo)
    } else {
      console.log('login')
      wx.login({
        success: function (res) {
          console.log('login success')
          console.log(res)
          wx.request({
            url: 'http://localhost/server/api/login.php',
            header: {
              'content-type': 'application/json'
            },
            data: {
              code: res.code
            },
            dataType: 'json',
            success: function (res) {
              console.log('server respose:')
              console.log(res)
              that.globalData = res.userInfo
              typeof cb === 'function' && cb(res.userInfo)
            },
            fail: function (failRes) {
              console.log('login failure')
              console.log(failRes)
            // that.getUserInfo(cb)
            }
          })
        },
        fail: function (res) {
          console.log('login failed')
          console.log(res)
        }
      })
    }
  },
  globalData: {
    userInfo: null
  }
})
