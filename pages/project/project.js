Page({
  data: {
    title: '跑步游戏化',
    src: 'http://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLnsYM6ibJ4QzKg0FaOUK4977khOcGic8kAJiaeLSNV6X5JS5SvjuQhxotadAFY9pYhqQ4UNvArB5Tuw/0'
  },
  qrscan: function () {
    wx.scanCode({
      success: function (result) {
        console.log(result)
      }
    })
  }
})
