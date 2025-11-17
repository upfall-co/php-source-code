<ul class="prd_list prd_nochk_list">

    <!------- 상품 리스트 - 테이블 tbody ------->
    <?php getOrderList(); ?>
    
    <!-- 배송비 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . shopFoldName . "/order/ship_pay.php" ?>
</ul>