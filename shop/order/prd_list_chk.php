<ul class="prd_list prd_chk_list">
    <!------- 상품 리스트 - 테이블 thead ------->
    <li class="thead">
        <ul class="table_column">
            <li class="td_chk">
                <input type="checkbox" id="prdChkAll" name="prdChkAll">
                <label for="prdChkAll">전제 선택</label>
            </li>
        </ul>
    </li>

    <!------- 상품 리스트 - 테이블 tbody ------->
    <?php getPrdChkList(); ?>

    <!-- 배송비 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/order/ship_pay.php" ?>
</ul>



<!-- <ul class="prd_list prd_chk_list">
    !------- 상품 리스트 - 테이블 thead -------
    <li class="thead">
        <ul class="table_column">
            <li class="td_chk">
                <input type="checkbox" id="prdChkAll" name="prdChkAll">
                <label for="prdChkAll"></label>
            </li>
            <li class="td_img">작품 이미지</li>
            <li class="td_name">작품명</li>
            <li class="td_option">주문옵션</li>
            <li class="td_frame">프레임</li>
            <li class="td_count">수량</li>
            <li class="td_price">금액</li>

            <li class="td_mo td_img">작품 이미지</li>
            <li class="td_mo td_option">옵션</li>

            <?php if ($prd_list_mdoe == "ORDER") {?>
                <li class="td_state">주문상태</li>
            <?php } ?>
        </ul>
    </li>

    !------- 상품 리스트 - 테이블 tbody -------
    ?php getPrdChkList(); ?
</ul> -->

<script>

</script>