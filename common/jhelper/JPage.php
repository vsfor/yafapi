<?php
namespace jhelper;

class JPage
{
    public $pageName = 'p'; //分页参数 key 如xxx.php?page=2中的page
    public $linkOnlyNum = false;//链接只返回 页面数字

    public $nextPage = '下一页'; //下一页
    public $prePage = '上一页'; //上一页
    public $firstPage = '首页'; //首页
    public $lastPage = '尾页'; //尾页
    public $preBar = '<<'; //上一分页条
    public $nextBar = '>>'; //下一分页条
    public $pageStep = 5; //分页条跳转的页数

    public $pageBarNum = 10; //控制记录条的个数。
    public $totalPage = 0; //总页数
    public $nowIndex = 0; //当前页
    public $urlParams = array(); //分页Url包含的其他 传递值
    public $nowNull = true; //当前页面 链接置空
    public $nowCss = 'current active'; //当前页面 链接 样式
    public $pagerCss = 'pagination'; //分页 ul 样式

    public function __construct($config=array())
    {
        foreach($config as $k => $v) {
            if(isset($this->$k)) $this->$k = $v;
        }
    }

    /**
     * 获取分页代码  -- 可以在此编辑自定义的模板
     * @param int $total 总记录数
     * @param int $perPage 每页显示的行数
     * @param int $pageBarNum 分页链接的个数
     * @param int $mode 显示风格，参数可为整数1，2，3任意一个
     * @return string
     */
    public function show($total = 0, $perPage=10, $pageBarNum=10, $mode=1)
    {
        if($total <= $perPage)
            return '';
        $this->doPage($total,$perPage,$pageBarNum);
        $res = '<ul class="'.$this->pagerCss.'">';
        switch ($mode)
        {
            case 1:
                $res .= $this->firstPage().$this->preBar().$this->prePage().$this->nowBar().$this->nextPage().$this->nextBar().$this->lastPage();
                break;
            case 2:
                $res .= $this->firstPage().$this->prePage().'<li class="'.$this->nowCss.'"><span>第'.$this->nowIndex.'页</span></li>'.$this->nextPage().$this->lastPage().'<li><span>第'.$this->select().'页</span></li>';
                break;
            default:
                $res .= $this->prePage().$this->nowBar().$this->nextPage();
                break;
        }
        return $res . '</ul>';
    }

    /**
     * 完成一些参数及必要值的初始化
     * @param $total
     * @param $perPage
     * @param $pageBarNum
     */
    protected function doPage($total, $perPage, $pageBarNum)
    {
        $this->totalPage = ceil($total/$perPage);
        $this->pageBarNum = $pageBarNum;
        $params = \Yaf\Dispatcher::getInstance()->getRequest()->getParams();
        if(isset($params[$this->pageName])) {
            $now = intval($params[$this->pageName]);
            $this->nowIndex = ($now > $this->totalPage || $now < 0) ? 1 : $now;
            unset($params[$this->pageName]);
        }
        $this->urlParams = $params;
        $this->nowIndex = ($this->nowIndex == 0) ? 1 : $this->nowIndex;
    }

    /**
     * 获取显示跳转按钮的代码
     * @return string
     */
    protected function select()
    {
        $return = '';
        if($this->totalPage > 1) {
            $return = '<select onChange="window.location=this.options[this.selectedIndex].value">';
            if($this->totalPage < 50) { // 解决 $this->totalPage 过大的问题
                for($i = 1; $i <= $this->totalPage; $i++) {
                    $selected = ($i == $this->nowIndex) ? ' selected="selected" ' : '';
                    $return .= '<option value="' . $this->pageUrl($i) . '" ' . $selected . '>' . $i . '</option>';
                }
            } else {
                $min = (($this->nowIndex - 10) < 2 ? 2 : ($this->nowIndex - 10));
                $max = (($this->nowIndex + 10) > ($this->totalPage - 1) ? ($this->totalPage - 1) : ($this->nowIndex + 10));
                $selected = (1 == $this->nowIndex) ? ' selected="selected" ' : '';
                $return .= '<option value="' . $this->pageUrl(1) . '" ' . $selected . '>' . 1 . '</option>';
                for($i = $min; $i <= $max; $i++) {
                    $selected = ($i == $this->nowIndex) ? ' selected="selected" ' : '';
                    $return .= '<option value="' . $this->pageUrl($i) . '" ' . $selected . '>' . $i . '</option>';
                }
                $selected = ($this->totalPage == $this->nowIndex) ? ' selected="selected" ' : '';
                $return .= '<option value="' . $this->pageUrl($this->totalPage) . '" ' . $selected . '>' . $this->totalPage . '</option>';
            }
            $return .= '</select>';
        }
        return $return;
    }

    /**
     * 获取页面跳转链接
     * @param $page
     * @return string
     */
    protected function pageUrl($page)
    {
        if($page == $this->nowIndex && $this->nowNull) {
            return 'javascript:;';
        }
        if($this->linkOnlyNum) return $page;
        $this->urlParams[$this->pageName] = $page;
        return Common::getLink($this->urlParams);
    }

    protected function firstPage()
    {
        return $this->pageHtml($this->pageUrl(1), $this->firstPage);
    }

    protected function prePage()
    {
        $p = $this->nowIndex - 1;
        $p = $p ? : 1;
        return $this->pageHtml($this->pageUrl($p), $this->prePage);
    }

    protected function preBar()
    {
        $p = $this->nowIndex - $this->pageStep;
        $p = $p > 0 ? $p : 1;
        return $this->pageHtml($this->pageUrl($p), $this->preBar);
    }

    protected function nowBar()
    {
        $res = '';
        if($this->pageBarNum <= 1) {
            return $res;
        }
        if($this->pageBarNum >= $this->totalPage) {
            for($p = 1; $p <= $this->totalPage; $p++) {
                $style = ($p == $this->nowIndex ?  $this->nowCss : '');
                $res .= $this->pageHtml($this->pageUrl($p), $p, $style);
            }
            return $res;
        }
        $zone = floor($this->pageBarNum/2);
        $min = $this->nowIndex - $zone;
        if($min < 1) {
            for($p = 1; $p <= $this->pageBarNum; $p++) {
                $style = ($p == $this->nowIndex ? $this->nowCss : '');
                $res .= $this->pageHtml($this->pageUrl($p), $p, $style);
            }
            return $res;
        }
        $max = $this->nowIndex + $zone;
        if($max > $this->totalPage) {
            for($p = $this->totalPage - $this->pageBarNum + 1; $p <= $this->totalPage; $p++) {
                $style = ($p == $this->nowIndex ?  $this->nowCss : '');
                $res .= $this->pageHtml($this->pageUrl($p), $p, $style);
            }
            return $res;
        }
        $max = ($this->pageBarNum % 2) ? ($max + 1) : $max;
        for($p = $min; $p < $max; $p++) {
            $style = ($p == $this->nowIndex ?  $this->nowCss : '');
            $res .= $this->pageHtml($this->pageUrl($p), $p, $style);
        }
        return $res;
    }

    protected function nextPage()
    {
        $p = $this->nowIndex + 1;
        $p = $p < $this->totalPage ? $p : $this->totalPage;
        return $this->pageHtml($this->pageUrl($p), $this->nextPage);
    }

    protected function nextBar()
    {
        $p = $this->nowIndex + $this->pageStep;
        $p = $p < $this->totalPage ? $p : $this->totalPage;
        return $this->pageHtml($this->pageUrl($p), $this->nextBar);
    }

    protected function lastPage()
    {
        return $this->pageHtml($this->pageUrl($this->totalPage), $this->lastPage);
    }

    protected function pageHtml($url,$text,$style = '')
    {
        $style = $style ? ' class="'.$style.'" ' : '';
        return '<li '.$style.'><a href="'.$url.'" '.$style.'>'.$text.'</a></li>';
    }
}