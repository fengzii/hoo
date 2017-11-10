<?php
/**
<div class="pagination">
						<span>« First</span>
						<span class="active">1</span>
						<a href="">2</a>
						<a href="">3</a>
						<a href="">4</a>
						<span>...</span>
						<a href="">23</a>
						<a href="">24</a>
						<a href="">Last »</a>
					</div>
*/
class cps_pager extends Pager {
    
    public $keyword    = 'page';
    public $first_page = 'First';
    public $last_page  = 'Last';
    public $pre_page   = 'Previous';
    public $next_page  = 'Next';
    
    function __toString()
    {
        $curr = $this->current();
        
        $string = '<div class="pagination">'."\n";
        $string.= '<span class="total">总数：'.(empty($this->total)?0:$this->total).'</span>';
        if ($this->page_num>1 && $curr>1){ // first
            $string.= '<a href="'.$this->get_link(1).'">« First</a>';
        } else {
            $string.= '<span>« First</span>';
        }
        
        if ($curr>1){ // pre
            $string.= '<a href="'.$this->get_link($this->current() - 1).'">Previous</a>';
        } else {
            $string.= '<span>Previous</span>';
        }

        $num = array();
        $display_num = 8;
        if ($this->page_num<1){
           $num[] = 1; 
        } elseif ($this->page_num <= $display_num){
            $num = range(1, $this->page_num);
        } elseif ($this->page_num - $curr <= $display_num ){
            $num = range($this->page_num - $display_num, $this->page_num);
        } else {
            $j = 0;
            for ($i=$curr; $i>0; $i--){
                $num[] = $i;
                $j++;
                if ($j>=ceil($display_num/2)) break;
            }
            $j = 0;
            for ($i=$curr+1; $i<=$this->page_num; $i++){
                $num[] = $i;
                $j++;
                if ($j>=floor($display_num/2)) break;            
            }
            sort($num);            
        }
        foreach ($num as $n){
            if ($curr==$n){
                $string.= '<span class="active">'.$n.'</span>'; 
            } else {
                $string.= '<a href="'.$this->get_link($n).'">'.$n.'</a>';
            }
        }
        if ($this->current() <= $this->page_num - 1){ // next
            $string.= '<a href="'.$this->get_link($this->current() + 1).'">Next</a>';
        } else {
            $string.= '<span>Next</span>'; 
        }
        if ($this->page_num > 1 && $curr!=$this->page_num){ // last
            $string.= '<a href="'.$this->get_link($this->page_num).'">Last »</a>';
        } else {
            $string.= '<span>Last »</span>';
        }
        $string.= '</div>';           
        return $string;
    }
}
