<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// 문의글 등록시 관리자에게 쪽지 전송
if($w == '' ) {
  $tmp_row = sql_fetch(" select max(me_id) as max_me_id from {$g5['memo_table']} ");
  $me_id = $tmp_row['max_me_id'] + 1;
  $me_memo = '['.$member['mb_id'].'] 님이 문의글을 등록하였습니다.\\n';
  $sql = " insert into {$g5['memo_table']}
  set me_id ='$me_id',
  me_recv_mb_id = '{$config['cf_admin']}',
  me_send_mb_id = '{$config['cf_admin']}',
  me_send_datetime = '".G5_TIME_YMDHIS."',
  me_memo = '$me_memo' ";
  sql_query($sql);
  }
?>


