
import java.io.IOException;
import java.net.URLDecoder;
import java.net.URLEncoder;

public class JeenGzip {


    public static String gzEncode(String str) throws IOException {
        String urlEncodeStr = URLEncoder.encode(str, "UTF-8").replaceAll("\\+","%20");
        byte[] gzEncodeBytes = ZLibUtils.compress(urlEncodeStr.getBytes());
        return Base64.encode(gzEncodeBytes);
    }

    public static String gzDecode(String str) throws IOException {
        byte[] base64DecodeBytes = Base64.decode(str);
        byte[] gzDecodeBytes = ZLibUtils.decompress(base64DecodeBytes);
        String decodeStr = new String(gzDecodeBytes);
        decodeStr = URLDecoder.decode(decodeStr,"UTF-8");
        return decodeStr;
    }

}
