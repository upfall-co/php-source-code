<ul class="prd_list prd_nochk_list">

  <!------- 상품 리스트 - 테이블 thead ------->
  <li class="thead">
    <ul class="table_column">
      <li class="td_img">작품 이미지</li>
      <li class="td_name">작품명</li>
      <li class="td_option">주문옵션</li>
      <li class="td_frame">프레임</li>
      <li class="td_count">수량</li>
      <li class="td_price">금액</li>

      <li class="td_mo td_img">작품 이미지</li>
      <li class="td_mo td_option">옵션</li>
    </ul>
  </li>

  <!------- 상품 리스트 - 테이블 tbody ------->
  <?php getOrderList(); ?>
</ul>