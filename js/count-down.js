// Generated by CoffeeScript 1.10.0
var _btn, countDown, time, timer;

if ($('.step2-btn').length > 0) {
  timer = null;
  time = 60;
  _btn = $('.step2-btn');
  countDown = function() {
    _btn.find('i').html(--time);
    if (time === 0) {
      clearInterval(timer);
      _btn.removeClass('disabled').html('重新发送');
      return time = 60;
    }
  };
  _btn.on('click', function() {
    if ($(this).hasClass('disabled')) {
      return false;
    }
    return $.ajax('?r=login/sendEmail', {
      type: 'POST',
      data: '',
      dataType: 'json',
      beforeSend: (function(_this) {
        return function() {
          return $(_this).addClass('disabled');
        };
      })(this),
      success: (function(_this) {
        return function(info) {
          var reset_email;
          reset_email = $('#reset_email').val();
          if (info.status === 0) {
            if (info.data.endTime) {
              time = info.data.endTime;
              $('.send-status').html('验证邮件发送错误');
              $('.send-tips').html("每次发送请间隔60秒，频繁发送可能会被当作垃圾邮件处理");
              _btn.addClass('disabled').html("重新发送（<i>" + time + "</i>）");
              return timer = setInterval(function() {
                return countDown();
              }, 1000);
            } else if (info.data.status === 4) {
              $('.send-status').html('验证邮件发送错误');
              $('.send-tips').html("当天最多发送5封邮件，请前往<a class=\"email-btn\" href=\"javascript:;\">" + reset_email + "</a>查收<br />注：若没有收到邮件，请留意是否被分类到垃圾邮件中。");
              return _btn.html('重新发送').addClass('disabled');
            }
          } else {
            $('.send-status').html("验证邮件发送成功");
            $('.send-tips').html("请前往<a class=\"email-btn\" href=\"javascript:;\">" + reset_email + "</a>查收，验证邮件24小时内有效<br />注：若没有收到邮件，请留意是否被分类到垃圾邮件中。");
            $(_this).addClass('disabled').html("重新发送（<i>" + time + "</i>）");
            return timer = setInterval(function() {
              return countDown();
            }, 1000);
          }
        };
      })(this),
      error: (function(_this) {
        return function() {
          layer.msg('系统错误！请刷新后重试或联系客服人员');
          return _btn.html('重新发送').removeClass('disabled');
        };
      })(this)
    });
  });
  _btn.trigger('click');
}