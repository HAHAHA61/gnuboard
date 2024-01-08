<?php
echo "내용입니댜";

$sub_menu = "700000";
require_once './_common.php';
auth_check_menu($auth, $sub_menu, 'r');


$g5['title'] = '유지보수문의관리';
require_once './admin.head.php';


$sql = "SELECT *
        FROM rainwrite_qa
        WHERE wr_id = '{$wr_id}' ";
 $row = sql_fetch($sql);
?>

<article id="bo_v" >
    <header>
        <h5 id="bo_v_title">
            
            <span class="bo_v_tit">
            <?php
            echo $row['wr_subject']; // 글제목 출력
            ?></span>
        </h5>
    </header>

    
    <section id="bo_v_atc">
        <h2 id="bo_v_atc_title">본문</h2>
        <div id="bo_v_share">
    
	    </div>

       
        <!-- 본문 내용 시작 { -->
        <div id="bo_v_con"><?php echo $row['wr_content']; ?></div>
        <div id="bo_v_con"><?php echo $row['wr_3']; ?></div>
        <div id="bo_v_con"><?php echo $row['wr_4']; ?></div>
        <div id="bo_v_con"><?php echo $row['wr_email']; ?></div>
        <div id="bo_v_con"><?php echo $row['wr_5']; ?></div>
       
        <!-- } 본문 내용 끝 -->



    </section>

    <!-- <?php
    $cnt = 0;
    if ($view['file']['count']) {
        for ($i=0; $i<count($view['file']); $i++) {
            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view'])
                $cnt++;
        }
    }
	?>

    <?php if($cnt) { ?>
    <!-- 첨부파일 시작 { -->
    <section id="bo_v_file">
        <h2>첨부파일</h2>
        <ul>
        <?php
        // 가변 파일
        for ($i=0; $i<count($view['file']); $i++) {
            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
         ?>
            <li>
               	<i class="fa fa-folder-open" aria-hidden="true"></i>
                <a href="<?php echo $view['file'][$i]['href'];  ?>" class="view_file_download">
                    <strong><?php echo $view['file'][$i]['source'] ?></strong> <?php echo $view['file'][$i]['content'] ?> (<?php echo $view['file'][$i]['size'] ?>)
                </a>
                <br>
                <span class="bo_v_file_cnt"><?php echo $view['file'][$i]['download'] ?>회 다운로드 | DATE : <?php echo $view['file'][$i]['datetime'] ?></span>
            </li>
        <?php
            }
        }
         ?>
        </ul>
    </section>
    <!-- } 첨부파일 끝 -->
    <?php } ?> -->

   
    
    


<?php
require_once './admin.tail.php';