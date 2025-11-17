<?php

namespace Clef;

class PagingAjax
{
    private $total      = 0;
    private $page       = 0;
    private $scale      = 0;
    private $start_page = 0;
    private $page_max   = 0;
    private $block      = 0;
    private $tails      = '';
    private $etc        = array();

    public $offset      = 0;
    public $size        = 0;

    public function __construct($total, $page, $arrParams='', $size=10, $scale=10, $etcParams=array()) {
        $this->total        = $total;
        $this->page         = $page;
        $this->size         = $size;
        $this->scale        = $scale;
        $this->page_max     = ceil($total / $size);
        $this->offset       = ($page - 1) * $size;
        $this->block        = floor( ($page - 1) / $scale );
        $this->no           = $this->total - $this->offset;

        if ( is_array($arrParams) ) {
            $this->tails    = http_build_query($arrParams);
        }

        if (!empty($etcParams)) {
            $this->etc      = $etcParams;
        }
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function getPaging() {
        if ( $this->block > 0 ) {
            $prev_block = ($this->block - 1) * $this->scale + 1;
            $op         = <<<HTML
                <li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="javascript:pagingAjax('{$this->etc['url']}', {$prev_block}, '{$this->tails}', '{$this->etc['id']}');">&lt;</a></li>
HTML;
        } else {
            $op         = '';
        }

        $this->start_page = $this->block * $this->scale + 1;
        for ($i=1; $i<=$this->scale && $this->start_page<=$this->page_max; $i++, $this->start_page++) {
            if ($this->start_page == $this->page) {
                $op     .= <<<HTML
                    <li class="page-item active"><a class="page-link" href="javascript:void(0);" onclick="javascript:pagingAjax('{$this->etc['url']}', {$this->start_page}, '{$this->tails}', '{$this->etc['id']}');">{$this->start_page}</a></li>
HTML;
            } else {
                $op     .= <<<HTML
                    <li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="javascript:pagingAjax('{$this->etc['url']}', {$this->start_page}, '{$this->tails}', '{$this->etc['id']}');">{$this->start_page}</a></li>
HTML;
            }
        }

        if ($this->page_max > ($this->block + 1) * $this->scale) {
            $next_block = ($this->block + 1) * $this->scale + 1;
            $op         .= <<<HTML
                <li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="javascript:pagingAjax('{$this->etc['url']}', {$next_block}, '{$this->tails}', '{$this->etc['id']}');">&gt;</a></li>
HTML;
        } else {
            $op         .= '';
        }

        return $op;
    }
}
