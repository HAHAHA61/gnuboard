<?php

$sub_menu = "700100";
require_once './_common.php';
auth_check_menu($auth, $sub_menu, 'r');


$g5['title'] = '유지보수문의관리';
require_once './admin.head.php';

$wr_id = $_GET['wr_id'];


// wr_id 값으로 글 불러오기
$sql = "SELECT *
        FROM rainwrite_qa
        WHERE wr_id = '{$wr_id}' ";
$row = sql_fetch($sql);

// wr_id에 달린 댓글 불러오기
$sql = "SELECT *
        FROM rainwrite_qa
        WHERE wr_parent = '{$wr_id}' AND wr_is_comment = 1";

$comment = sql_fetch($sql);


$sql = " SELECT * 
         FROM rainboard_file 
         where wr_id = '{$wr_id}' ";
$file = sql_fetch($sql);

$comment_action_url = "http://raineye.com/adm/maintenance_view.php?wr_id=".$wr_id;

// 댓글 폼이 제출되었을 때의 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 필요한 POST 데이터 확인 (wr_id는 이미 사용 중)
    $wr_content = isset($_POST['wr_content']) ? $_POST['wr_content'] : '';
    $mb_id = $member['mb_id'];

    // wr_content가 비어있는지 확인
    if (empty($wr_content)) {
        echo "댓글 내용을 입력해주세요11.";
    } else {
        // 데이터베이스에 댓글 추가하는 INSERT 쿼리
        $sql = "INSERT INTO rainwrite_qa 
                SET 
                    wr_parent = '{$wr_id}', 
                    wr_is_comment = 1, 
                    wr_comment = 1, 
                    wr_content = '{$wr_content}',
                    mb_id = '$mb_id',
                    wr_datetime = '".G5_TIME_YMDHIS."',
                    wr_last = '".G5_TIME_YMDHIS."' ";
        sql_query($sql);
        $comment_id = sql_insert_id();
         // 원글에 댓글수 증가 & 마지막 시간 반영
        sql_query(" update rainwrite_qa set wr_comment = wr_comment + 1, wr_last = '".G5_TIME_YMDHIS."' where wr_id = '$wr_id' ");
        // 댓글 1 증가
        sql_query(" update {$g5['board_table']} set bo_count_comment = bo_count_comment + 1 where bo_table = '$bo_table' ");
        

        // 댓글 등록 후 다시 읽기 페이지로 리다이렉션 (선택)
        $redirect_url = "http://raineye.com/adm/maintenance_view.php?wr_id=".$wr_id;

        goto_url($redirect_url);

        exit;
    }
}


?>

<article id="bo_v">
    

    
            <!-- 여기에 필요한 내용 추가 -->
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
                        <td>이미지</td>
                        <td>
                        <?php if (isset($file['bf_source']) && $file['bf_source']) : ?>
                            <a href="<?php echo G5_DATA_URL.'/file/'.$file['bf_file']; ?>" download>
                                이미지 다운로드
                            </a>
                        <?php else : ?>
                            이미지가 없습니다.
                        <?php endif; ?>
                    </td>
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
                        <td><?php echo ($row['wr_comment'] == 1) ? "Y" : "N"; ?>
                        </td>
                    </tr>
                    <!-- 댓글 출력 -->
                    <tr>
                        <td>답변</td>
                        <td>
                            <?php 
                            if ($row['wr_comment'] == 1) {
                                echo $comment['wr_content'];
                            }else{
                                
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>

            </table>


            
        </div>
</article>
<!-- 댓글 쓰기 시작 { -->
    <aside id="bo_vc_w" class="bo_vc_w">
    <form name="fviewcomment" id="fviewcomment" action="<?php echo $comment_action_url; ?>" onsubmit="return fviewcomment_submit(this);" method="post" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w ?>" id="w">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
    <input type="hidden" name="comment_id" value="<?php echo $c_id ?>" id="comment_id">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="is_good" value="">

    <span class="sound_only">내용</span>
    
    <textarea id="wr_content" name="wr_content" maxlength="10000" required class="required" title="내용" placeholder="댓글내용을 입력해주세요" >
        <?php if ($row['wr_comment'] ==1) {
            echo $comment['wr_content'];
        } else{
            
        } ?>
    </textarea>
    <script>
    $(document).on("keyup change", "textarea#wr_content[maxlength]", function() {
        var str = $(this).val()
        var mx = parseInt($(this).attr("maxlength"))
        if (str.length > mx) {
            $(this).val(str.substr(0, mx));
            return false;
        }
    });
    </script>
    
    <script>
        var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자
    document.getElementById('wr_content').value = document.getElementById('wr_content').value.replace(pattern, "");

    $(document).on("keyup change", "textarea#wr_content[maxlength]", function() {
        var str = $(this).val()
        var mx = parseInt($(this).attr("maxlength"))
        if (str.length > mx) {
            $(this).val(str.substr(0, mx));
            return false;
        }
    });
    </script>
    <div class="bo_vc_w_wr">
        <div class="btn_confirm">
            <button type="submit" id="btn_submit" class="btn_submit">댓글등록</button>
        </div>
    </div>
    </form>
</aside>

<script>
    var save_before = '';
    var save_html = document.getElementById('bo_vc_w').innerHTML;

    function fviewcomment_submit(f)
    {
        var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자

        f.is_good.value = 0;

        var subject = "";
        var content = "";
        $.ajax({
            url: g5_bbs_url+"/ajax.filter.php",
            type: "POST",
            data: {
                "subject": "",
                "content": f.wr_content.value
            },
            dataType: "json",
            async: false,
            cache: false,
            success: function(data, textStatus) {
                subject = data.subject;
                content = data.content;
            }
        });
        if (!document.getElementById('wr_content').value)
    {
        alert("댓글을 입력하여 주십시오.");
        return false;
    }
    // 캡차 확인
    <?php if($is_guest) echo chk_captcha_js();  ?>
    
    // 댓글 토큰 설정
    set_comment_token(f);

    document.getElementById("btn_submit").disabled = "disabled";

    return true;
    }
    </script>
    <script>
jQuery(function($) {            
    //댓글열기
    $(".cmt_btn").click(function(e){
        e.preventDefault();
        $(this).toggleClass("cmt_btn_op");
        $("#bo_vc").toggle();
    });
});
</script>

<?php
require_once './admin.tail.php';