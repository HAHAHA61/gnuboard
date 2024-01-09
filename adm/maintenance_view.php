<?php
echo "내용입니댜";

$sub_menu = "700100";
require_once './_common.php';
auth_check_menu($auth, $sub_menu, 'r');


$g5['title'] = '유지보수문의관리';
require_once './admin.head.php';

$wr_id = $_GET['wr_id'];


$sql = "SELECT *
        FROM rainwrite_qa
        WHERE wr_id = '{$wr_id}' ";
 $row = sql_fetch($sql);
?>

<article id="bo_v">
    

    
            <!-- 여기에 필요한 내용 추가 -->
        </div>
        <div class="tbl_head01 tbl_wrap">
            <table>
                <thead>
                    <tr>
                        <th>제목</th>
                        <th><?php echo $row['wr_subject']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    
                    <tr>
                        <td>본문</td>
                        <td><?php echo $row['wr_content']; ?></td>
                    </tr>
                    <tr>
                        <td>업체명</td>
                        <td><?php echo $row['wr_3']; ?></td>
                    </tr>
                    <tr>
                        <td>홈페이지 주소</td>
                        <td><?php echo $row['wr_4']; ?></td>
                    </tr>
                    <tr>
                        <td>담당자 이메일</td>
                        <td><?php echo $row['wr_email']; ?></td>
                    </tr>
                    <tr>
                        <td>담당자 전화번호</td>
                        <td><?php echo $row['wr_5']; ?></td>
                    </tr>
                    <tr>
                        <td>답변유무</td>
                        <td><?php 
                                if ($row['wr_comment'] == 1){
                                    echo "Y";
                                }else{
                                    echo "N";
                                }
                            ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
</article>


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