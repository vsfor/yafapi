
import java.io.IOException;
import java.net.URI;
import java.net.URLEncoder;


/**
 * ZLib压缩测试用例
 *
 * @author <a href="mailto:zlex.dongliang@gmail.com">梁栋</a>
 * @version 1.0
 * @since 1.0
 */
public class ZLibUtilsTest {

    // 测试方法
    public static void main(String[] args) throws IOException {
        String apiStr = "eJw9jWEOwjAIRk/zfq6ZdB3tz7HiNYxOvf8RVjRZwvsSAg9QQ+Q5IG+RvtIKNiM2IXfKHuWKGXWJDdlHvtJxOUd6I3MafEK3G7WNBi9snabf6UHul4ovWI2R2O/dStX/sYz2E5gdHgA=";
        System.out.println("Api Json: \t" + JeenGzip.gzDecode(apiStr));


        System.out.println("Api compressed:\t" + JeenGzip.gzEncode("{\"a\":\"数+-\\/\\\\组\",\"b.c\":\"c.d . e汉 字f-_=\",\"中+文\":3}"));

        System.out.println(URLEncoder.encode("%20","UTF-8"));
        //{"a":"a b +c"}
        System.out.println(URLEncoder.encode("a b +c","UTF-8"));
    }


}

