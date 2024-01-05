<?php
if (!defined('_INDEX_')) define('_INDEX_', true);
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/index.php');
    return;
}

if(G5_COMMUNITY_USE === false) {
    include_once(G5_THEME_SHOP_PATH.'/index.php');
    return;
}

include_once(G5_THEME_PATH.'/head.php');
?>

<h2 class="sound_only">최신글</h2>

<div class="latest_top_wr">
    <!-- <?php
    // 최신글 출력 함수 (latest) 사용
    echo latest('theme/pic_list', 'notice', 4, 23); // 공지사항 게시판 최신글
?> -->
</div>
<div class="latest_wr">
    <!-- 사진 최신글2 { -->
    <!-- <?php
    // 갤러리 게시판 최신글은 출력하지 않음
    // echo latest('theme/pic_block', 'gallery', 4, 23);
    ?> -->
    <!-- } 사진 최신글2 끝 -->
</div>

<div class="latest_wr">
    <!-- 최신글 시작 { -->
    <?php
    // 최신글
    $sql = " select bo_table
                from `{$g5['board_table']}` a left join `{$g5['group_table']}` b on (a.gr_id=b.gr_id)
                where a.bo_device <> 'mobile' ";
    if(!$is_admin)
        $sql .= " and a.bo_use_cert = '' ";
    $sql .= " and a.bo_table not in ('notice', 'gallery') ";     // 공지사항과 갤러리 게시판은 제외
    $sql .= " order by b.gr_order, a.bo_order ";
    $result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $lt_style = '';
        if ($i%3 !== 0 ) $lt_style = "margin-left:2%";
    ?>
    <div style="float:left;<?php echo $lt_style ?>" class="lt_wr">
        <?php
        // 최신글 출력 함수 (latest) 사용
        // wr_2 필드가 비어있는 게시글만 출력
        echo latest('theme/basic', $row['bo_table'], 6, 24, '', '', true, false, 'wr_2 IS NULL OR wr_2 = ""');
        ?>
    </div>
    <?php
    }
    ?>
    <!-- } 최신글 끝 -->
</div>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
