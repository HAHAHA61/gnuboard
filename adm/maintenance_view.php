<?php

$sub_menu = "700100";
require_once './_common.php';
include_once(G5_LIB_PATH.'/thumbnail.lib.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');
include_once(G5_PHPMAILER_PATH.'/PHPMailerAutoload.php');
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

$sql = "SELECT * FROM rainwrite_qa WHERE wr_parent = '{$wr_id}' AND wr_is_comment = 1 ORDER BY wr_datetime ASC";
$comments = sql_query($sql);


$sql = " select * from rainboard where 1";
$board = sql_fetch($sql);



$view = get_view($row, $board, $board_skin_path);


$comment_action_url = "http://raineye.com/adm/maintenance_view.php?wr_id=".$wr_id;

function sendEmailToOriginalPostAuthor($recipient, $subject, $message) {
    global $config; // $config가 코드의 다른 위치에서 정의되어 있다고 가정합니다.

    // 메일러 매개변수 생성
    $fname = "최고관리자";
    $fmail = "iissdn55@gmail.com";
    $to = $recipient;
    $content = $message;
    $type = 1; // HTML 이메일로 가정합니다.
    $file = ""; // 필요시 파일을 첨부할 수 있습니다.
    $cc = "";
    $bcc = "";

    // 메일러 함수를 사용하여 이메일 보내기
    $mail_send_result = mailer($fname, $fmail, $to, $subject, $content, $type, $file, $cc, $bcc);

    if (!$mail_send_result) {
        echo "메일 전송에 실패했습니다.";
    } else {
        echo "메일이 성공적으로 전송되었습니다.";
    }
}





// 댓글 수정 폼이 제출되었을 때의 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['w']) && $_POST['w'] == 'cu'){
    $comment_id = (int)$_POST['wr_id'];

    // 댓글 ID로 해당 댓글 정보 가져오기
    $sql = "SELECT * FROM rainwrite_qa WHERE wr_parent = '{$wr_id}' AND wr_is_comment = 1";
    $comment_info = sql_fetch($sql);
    $comment_id = $comment_info['wr_id'];


    if (!$comment_info) {
        echo "해당 댓글을 찾을 수 없습니다.";
        exit;
    }

    // 필요한 POST 데이터 확인
    $wr_content = isset($_POST['wr_content']) ? $_POST['wr_content'] : '';
    $mb_id = $member['mb_id'];

    // wr_content가 비어있는지 확인
    if (empty($wr_content)) {
        echo "댓글 내용을 입력해주세요.";
    } else {
        // 댓글 수정을 위한 UPDATE 쿼리
        $sql = "UPDATE rainwrite_qa 
                SET 
                    wr_content = '{$wr_content}',
                    mb_id = '$mb_id',
                    wr_last = '".G5_TIME_YMDHIS."' 
                WHERE 
                    wr_id = '{$comment_id}'";

        sql_query($sql);

        // 댓글 수정 후 다시 읽기 페이지로 리다이렉션 (선택)
        $redirect_url = "http://raineye.com/adm/maintenance_view.php?wr_id=".$wr_id;

        sendEmailToOriginalPostAuthor($row['wr_email'], "[레인아이]내 글에 달린 댓글이 수정되었습니다.", " 댓글이 수정되었습니다. 확인해주세요.");


        goto_url($redirect_url);
        exit;
    }
}else{
    // 필요한 POST 데이터 확인 (wr_id는 이미 사용 중)
    $wr_content = isset($_POST['wr_content']) ? $_POST['wr_content'] : '';
    $mb_id = $member['mb_id'];
    $wr_name = $member['mb_nick'];
    $wr_email = $member['mb_email'];

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
                    wr_name = '$wr_name',
                    wr_email = '$wr_email',
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

        // 원본 게시물 작성자에게 이메일 보내기
        sendEmailToOriginalPostAuthor($row['wr_email'], "[레인아이]내 글에 새로운 댓글이 등록되었습니다.", "새로운 댓글이 등록되었습니다. 확인해주세요.");
        
        goto_url($redirect_url);

        exit;
    }
}
}
?>

<article id="bo_v">
            <!-- 여기에 필요한 내용 추가 -->
            <div class="btn_confirm">
                <button type="submit" id="btn_submit" class="btn_submit" style="float: left; background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-bottom: 20px; text-align: center;" onclick="window.location.href='http://raineye.com/adm/maintenance.php';">목록으로</button>
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
                    <td>이미지</td>
                    <td>
                    <?php
                        // 파일 출력
                        
                       
                        
                        $v_img_count = count($view['file']);
                        if($v_img_count) {
                            echo "<div id=\"bo_v_img\">\n";
                
                            foreach($view['file'] as $view_file) {
                                echo get_file_thumbnail($view_file);
                            }
                            echo "</div>\n";
                        }
                       
                        ?>      
                    </td>
                    </tr>
                    <td>첨부파일</td>              
                    
                        <td>
                                <?php
                            $cnt = 0;
                            if ($view['file']['count']) {
                                for ($i=0; $i<count($view['file']); $i++) {
                                    if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view'])
                                        $cnt++;
                                }
                            }
                            ?>
                            <?php if($cnt) { ?>
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
                            <?php } ?>
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
                        <td><?php echo ($row['wr_comment'] >= 1) ? "Y" : "N"; ?>
                        </td>
                    </tr>

        
                    <!-- 댓글 출력 -->
                    <tr>
                        <td>답변</td>
                        <td>
                            <?php 
                            if ($row['wr_comment'] >= 1){
                                echo $comment['wr_content'];
                                
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
            <?php
            if ($row['wr_comment'] >= 1) {
                // 댓글이 존재하면 "댓글수정" 버튼 표시
                echo '<button type="submit" id="btn_submit" class="btn_submit" style="float:right;" onclick="document.getElementById(\'w\').value=\'cu\';">수정완료</button>';

            } else {
                // 댓글이 없으면 "댓글등록" 버튼 표시
                echo '<button type="submit" id="btn_submit" class="btn_submit" style="float:right;">댓글등록</button>';
            }
            ?>
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

    function comment_box(comment_id, work)
{
    var el_id,
        form_el = 'fviewcomment',
        respond = document.getElementById(form_el);

    // 댓글 아이디가 넘어오면 답변, 수정
    if (comment_id)
    {
        if (work == 'c')
            el_id = 'reply_' + comment_id;
        else
            el_id = 'edit_' + comment_id;
    }
    else
        el_id = 'bo_vc_w';

    if (save_before != el_id)
    {
        if (save_before)
        {
            document.getElementById(save_before).style.display = 'none';
        }

        document.getElementById(el_id).style.display = '';
        document.getElementById(el_id).appendChild(respond);
        //입력값 초기화
        document.getElementById('wr_content').value = '';
        
        // 댓글 수정
        if (work == 'cu')
        {
            document.getElementById('wr_content').value = document.getElementById('save_comment_' + comment_id).value;
            if (typeof char_count != 'undefined')
                check_byte('wr_content', 'char_count');
            if (document.getElementById('secret_comment_'+comment_id).value)
                document.getElementById('wr_secret').checked = true;
            else
                document.getElementById('wr_secret').checked = false;
        }

        document.getElementById('comment_id').value = comment_id;
        document.getElementById('w').value = work;

        if(save_before)
            $("#captcha_reload").trigger("click");

        save_before = el_id;
    }
}

function comment_delete()
{
    return confirm("이 댓글을 삭제하시겠습니까?");
}

if($is_admin){
comment_box('', 'c'); // 댓글 입력폼이 보이도록 처리하기위해서 추가 (root님)
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