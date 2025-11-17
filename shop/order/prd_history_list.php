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