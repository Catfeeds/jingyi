/**
 * Created by Administrator on 2018/1/25.
 */
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r !== null)
        return  unescape(r[2]);
    return null;
}