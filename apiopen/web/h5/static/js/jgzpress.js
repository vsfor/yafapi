/**
 * Created by jeen on 16-6-11.
 * 依赖于zlib_and_gzip.min.js
 */

/**
 * String to Byte Array
 * @param str
 * @returns {Array}
 */
function stringToByteArray(str) {
    var array = new (window.Uint8Array !== void 0 ? Uint8Array : Array)(str.length);
    var i;
    var il;

    for (i = 0, il = str.length; i < il; ++i) {
        array[i] = str.charCodeAt(i) & 0xff;
    }

    return array;
}

/**
 * Byte Array to String
 * @param arr
 * @returns {string}
 */
function byteArrayToString(arr) {
    var str = "";
    var i;
    var il;

    for (i = 0, il = arr.length; i<il; ++i) {
        str += String.fromCharCode(arr[i]);
    }

    return str;
}

/**
 * 解压 php  经过  base64_encode( gzcompress( rawurlencode( json_encode( str))))  编码的字符串
 * @param str
 * @returns {string}
 */
function gzuncompress(str) {
    var data = atob(str); //base64_decode
    var byteArr = stringToByteArray(data);
    var inf = new Zlib.Inflate(byteArr);
    var infByteArr = inf.decompress(); //gzuncompress
    var infStr = byteArrayToString(infByteArr);
    var uriDecoded = decodeURIComponent(infStr); //urldecode
    return JSON.parse(uriDecoded); //json_decode
}

/**
 * 压缩 str 生成 可使用php 进行  json_decode( rawurldecode( gzuncompress( base64_decode( str))))  解码的字符串
 * @param obj String|Array|Object
 * @returns {string}
 */
function gzcompress(obj) {
    var jsonStr = JSON.stringify(obj); //json_encode
    var uriEncoded = encodeURIComponent(jsonStr); //urlencode
    var data = stringToByteArray(uriEncoded);
    var def = new Zlib.Deflate(data);
    var defData = def.compress(); //gzcompress
    var defStr = byteArrayToString(defData);
    return btoa(defStr); //base64_encode
}