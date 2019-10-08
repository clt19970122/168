/**
 * Form set Array to Key-value
 * @param {type} data
 * @returns {unresolved}
 */
function objToArray(data) {
    var o = {};
    $.each(data, function () {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
}

/**
 * 验证手机
 * @param {type} phone
 * @returns {undefined}
 */
function checkPhone(phone) {
    var myreg = /^1[3456789]{1}\d{9}$/;
    if (!myreg.test(phone)) {
        return false;
    }
    return true;
}

/**
 * 设置cookie
 * @param {type} name
 * @param {type} value
 * @returns {undefined}
 */
function setCookie(name, value) {
    var Days = 30;
    var exp = new Date();
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + escape(value) + ";path=/;expires=" + exp.toGMTString();
}

/**
 * 获取cookie
 * @param {type} name
 * @returns {unresolved}
 */
function getCookie(name) {
    var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
    if (arr = document.cookie.match(reg)) {
        return unescape(arr[2]);
    }
    return null;
}

/**
 * 删除cookie
 * @param {type} name
 * @returns {undefined}
 */
function delCookie(name) {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval = getCookie(name);
    if (cval !== null)
        document.cookie = name + "=" + cval + ";path=/;expires=" + exp.toGMTString();
}

/**
 * ajax自动加载跳转
 * @param {type} configURL
 * @param {type} data
 * @returns {undefined}
 */
function ajaxRt(configURL, data) {
    $.ajax({
        url: configURL,
        type: "POST",
        data: data,
        dataType: "json",
        success: function (res) {
            console.log(res);
            if (res.status !== 1) {
                alert(res.msg);
                return;
            }
            if (res.url === 1) {
                window.location.reload();
                return;
            }
            window.location.href = res.data;
        },
        error: function (st) {
            console.log(st);
        }
    });
}

////////////////////////////////////////////////////////////////////////////////

/**
 * 时间转时间戳
 * @param {type} date
 * @returns {undefined}
 */
function dateToTstamp(date) {
    var tmp = new Date(date);
    var time = tmp.getTime();
    return time / 1000;
}

function showPreLoading() {
    $("#loadingToast").fadeIn(100);
}

function hidePreLoading() {
    $("#loadingToast").fadeOut(100);
}

function doLocalToast(title) {
    $("#do-toast").html(title);
    $("#do-toast").removeClass("none");
    setTimeout(function () {
        $("#do-toast").addClass("none");
    }, 1000);
}

/**
 * 微信定制弹窗
 * @param {type} title
 * @returns {undefined}
 */
function alert_wec(title) {
    $("#iosDialogTitle").html(title);
    $('#iosDialog').fadeIn(200);
}

/**
 * 点击取消弹窗
 */
$(document).on('click', '.weui-dialog__btn', function () {
    $(this).parents('.js_dialog').fadeOut(200);
});

