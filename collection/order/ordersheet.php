<?php
define("SUB", "ORDER");
include_once $_SERVER['DOCUMENT_ROOT'] . '/php/ordersheet_code.php';

include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/header.php";
?>

<body>
    <!-- 상단 헤더 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/gmenu.php" ?>

    <main id="order">
        <div class="wrapper_1400">
            <div class="sub_title eng">Order</div>

            <section class="order_section">
                <div class="order_title">주문상품 정보</div>
                <!-- 상품 리스트 -->
                <?php include_once "./prd_list.php" ?>

                <!-- 최종 결제 금액 -->
                <?php include_once "./final_pay_price.php" ?>
            </section>

            <form id="frm" method="post" action="/php/order.php" enctype="multipart/form-data">
                <input type="hidden" id="mode" name="mode" value="INS"/>
                <input type="hidden" id="SEQ" name="SEQ" value="<?=$PRODUCT_TEMP_SEQ?>"/>
                <input type="hidden" id="PRODUCTS" name="PRODUCTS" value="<?=$PRODUCTS?>"/>
                <input type="hidden" id="PAGE_TYPE" name="PAGE_TYPE" value="<?=PAGE1?>"/>
                <input type="hidden" id="TYPE" name="TYPE" value="10"/>
                <input type="hidden" id="TOTAL_COUNT" name="TOTAL_COUNT" value="<?=$_db_TOTAL_COUNT?>"/>
                <input type="hidden" id="TOTAL_PRICE" name="TOTAL_PRICE" value="<?=$_db_TOTAL_PRICE?>"/>
                <input type="hidden" id="REAL_DLVY_PRICE" name="REAL_DLVY_PRICE" value="<?=$REAL_DLVY_PRICE?>"/>

                <section class="order_section half_section remember_data">
                    <div class="half">
                        <div class="order_title">주문자 정보 입력</div>

                        <ul class="form_wrap">
                            <li>
                                <div class="form_label_ani">
                                    <label title="Name">이름 *</label>
                                    <input id="order_name" name="order_name" type="text" value="<?= $NAME ?>">
                                    <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                </div>
                            </li>

                            <li>
                                <div class="form_label_ani">
                                    <label title="Tel">휴대폰 *</label>
                                    <input id="order_tel" name="order_tel" type="tel" value="<?= $MOBILE ?>" maxlength="13" oninput="autoHyphen(this)">
                                    <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                </div>
                            </li>

                            <li>
                                <div class="form_label_ani">
                                    <label title="Email">이메일 *</label>
                                    <input id="order_email" name="order_email" type="email" value="<?= $EMAIL ?>">
                                    <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="half">
                        <div class="order_title">배송정보 입력</div>

                        <div class="orderer_same_chk input_chk">
                            <input type="checkbox" id="ordererSameChk" onchange="ordererChange(this)">
                            <label for="ordererSameChk">주문자 정보와 동일</label>
                        </div>

                        <ul class="form_wrap">
                            <li>
                                <div class="form_label_ani">
                                    <input id="DLVY_NAME" name="DLVY_NAME" type="text" placeholder="이름">
                                    <label title="Name">이름 *</label>
                                    <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                </div>
                            </li>

                            <li>
                                <div class="form_label_ani">
                                    <input id="DLVY_MOBILE" name="DLVY_MOBILE" type="tel" maxlength="13" oninput="autoHyphen(this)" placeholder="휴대폰">
                                    <label title="Tel">휴대폰 *</label>
                                    <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                </div>
                            </li>

                            <li class="with_btn">
                                <div class="form_label_ani no_float">
                                    <input id="DLVY_ADDRESS_ZIPCODE" name="DLVY_ADDRESS_ZIPCODE" type="text" placeholder="주소" readonly>
                                    <label title="Address">주소 *</label>
                                    <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                </div>
                                <button type="button" id="addressBtn" class="border_btn" onclick="execDaumPostcode()">주소검색</button>
                            </li>

                            <li>
                                <div class="form_label_ani no_float">
                                    <input id="DLVY_ADDRESS" name="DLVY_ADDRESS" type="text" placeholder="기본주소" readonly>
                                    <label title="Address">기본주소</label>
                                    <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                </div>
                            </li>

                            <li>
                                <div class="form_label_ani">
                                    <input id="DLVY_ADDRESSDETAIL" name="DLVY_ADDRESSDETAIL" type="text" placeholder="상세주소">
                                    <label title="Address">상세주소</label>
                                    <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                </div>
                            </li>

                            <li>
                                <div class="form_label_ani">
                                    <input id="DLVY_MESSAGE" name="DLVY_MESSAGE" type="text" placeholder="배송메세지">
                                    <label title="Address">배송메세지</label>
                                    <div class="erase_btn" onclick="eraseThisForm(this);"><img src="<?= artFoldName ?>/img/erase_icon.png" alt="내용 지우기"></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </section>

                <section class="order_section">
                    <div class="order_title">개인정보처리방침</div>

                    <ul class="agree_chk_wrap">
                        <li class="input_chk">
                            <input type="checkbox" id="agreeChkAll" name="agreeChkAll">
                            <label for="agreeChkAll">전체동의</label>
                        </li>
                        <li class="input_chk">
                            <input type="checkbox" id="agreeChk1" name="agreeChk1" value = "Y">
                            <label for="agreeChk1">(필수)주식회사 글린트의 정책 및 이용약관에 동의합니다.</label>
                            <div class="agree_pop_btn" onclick="onAgreePop1();">전문보기</div>
                        </li>
                        <li class="input_chk">
                            <input type="checkbox" id="agreeChk2" name="agreeChk2" value = "Y">
                            <label for="agreeChk2">(선택)개인정보수집 및 취급방침에 동의합니다.</label>
                            <div class="agree_pop_btn" onclick="onAgreePop2();">전문보기</div>
                        </li>
                    </ul>

                    <!-- 개인정보처리방침 팝업 -->
                    <?php include_once "./agree_popup1.php" ?>
                    <?php include_once "./agree_popup2.php" ?>
                </section>

                <section class="order_section">
                    <div class="order_title">결제수단 선택</div>

                    <div class="payment_wrap">
                        <ul class="payment_type">
                            <li>
                                <input type="radio" name="orderPayType" id="orderPayType1" value="payCard" checked>
                                <label for="orderPayType1">
                                    <img src="<?= artFoldName ?>/img/mypage/payment_icon1.png" alt="신용카드 결제">
                                    <span>신용카드 결제</span>
                                </label>
                            </li>

                            <li>
                                <input type="radio" name="orderPayType" id="orderPayType2" value="payAccount">
                                <label for="orderPayType2">
                                    <img src="<?= artFoldName ?>/img/mypage/payment_icon2.png" alt="실시간 계좌이체">
                                    <span>실시간 계좌이체</span>
                                </label>
                            </li>

                            <li>
                                <input type="radio" name="orderPayType" id="orderPayType3" value="payNoBankbook">
                                <label for="orderPayType3">
                                    <img src="<?= artFoldName ?>/img/mypage/payment_icon3.png" alt="무통장 입금">
                                    <span>무통장 입금</span>
                                </label>
                            </li>
                        </ul>

                        <div class="payment_cont">
                            <!----- 신용카드 결제 ----->
                            <div class="payment_info payment_info1">
                                신용카드 결제/간편 결제(카카오페이, 네이버페이, 토스페이)
                            </div>

                            <!----- 실시간 계좌이체 ----->
                            <div class="payment_info payment_info2">
                                실시간 계좌이체
                            </div>

                            <!----- 무통장 입금 ----->
                            <div class="payment_info payment_info3">
                                <ul class="form_wrap">
                                    <li>
                                        <div class="form_label_ani no_float just_input">
                                            <!-- <label>입금은행 선택 *</label>
                                            <input type="text" id="NO_BANK_CD" name="NO_BANK_CD"> -->
                                            <select class="form-control" id="NO_BANK_CD" name="NO_BANK_CD">
                                                <?php gfn_getComboList("은행리스트", "AD007", "", "선택", "", "", "Y") ?>
                                            </select>
                                        </div>
                                    </li>

                                    <li>
                                        <div class="form_label_ani no_float just_input">
                                            <input type="text" id="NO_BANK_DEPOSITOR" name="NO_BANK_DEPOSITOR" placeholder="입금자 명">
                                            <label>입금자 명 *</label>
                                        </div>
                                    </li>

                                    <li class="with_radio">
                                        <p class="">발급정보 *</p>
                                        <ul class="radio_wrap">
                                            <li class="input_radio">
                                                <input type="radio" name="issuance" id="issuanceNo" value="issuanceNo" checked>
                                                <label for="issuanceNo">발급안함</label>
                                            </li>
                                            <li class="input_radio">
                                                <input type="radio" name="issuance" id="issuanceCash" value="issuanceCash">
                                                <label for="issuanceCash">현금영수증</label>
                                            </li>
                                            <li class="input_radio">
                                                <input type="radio" name="issuance" id="issuanceTax" value="issuanceTax">
                                                <label for="issuanceTax">세금계산서</label>
                                            </li>

                                            <!-- 현금영수증 선택 시 -->
                                            <li class="pay_depth2 chk_cash">
                                                <ul class="radio_wrap">
                                                    <li class="input_radio">
                                                        <input type="radio" name="cashReceipt" id="cashReceipt1" value="cashReceipt1" checked>
                                                        <label for="cashReceipt1">개인 소득공제</label>
                                                    </li>
                                                    <li class="input_radio">
                                                        <input type="radio" name="cashReceipt" id="cashReceipt2" value="cashReceipt2">
                                                        <label for="cashReceipt2">사업자 지출증빙</label>
                                                    </li>
                                                </ul>

                                                <!-- 현금영수증 > 개인 소득공제 선택 시 -->
                                                <ul class="pay_depth3 chk_cash_type1">
                                                    <li>
                                                        <div class="lt">휴대폰 *</div>
                                                        <div class="rt"><input type="tel" id="CASH_MOBILE" name="CASH_MOBILE" maxlength="13" oninput="autoHyphen(this)"></div>
                                                    </li>
                                                    <li>
                                                        <div class="lt">이메일 *</div>
                                                        <div class="rt"><input type="email" id="CASH_EMAIL" name="CASH_EMAIL"></div>
                                                    </li>
                                                </ul>
                                                <!-- 현금영수증 > 사업자 지출증빙 선택 시 -->
                                                <ul class="pay_depth3 chk_cash_type2">
                                                    <li>
                                                        <div class="lt">사업자번호 *</div>
                                                        <div class="rt"><input type="text" id="CASH_BUSINESS" name="CASH_BUSINESS"></div>
                                                    </li>
                                                    <li>
                                                        <div class="lt">이메일 *</div>
                                                        <div class="rt"><input type="email" id="CASH_EMAIL2" name="CASH_EMAIL2"></div>
                                                    </li>
                                                </ul>
                                            </li>

                                            <!-- 세금계산서 선택 시 -->
                                            <li class="pay_depth2 chk_tax">
                                                <ul class="pay_depth3 chk_tax_type1">
                                                    <li>
                                                        <div class="lt">사업자등록증 *</div>
                                                        <div class="rt">
                                                            <input type="file" id="ATTACH" name="ATTACH" onchange="javascript:fileupload(this, '', 'jpg|jpeg|png|pdf', 1);">
                                                            <p>jpg, jpeg, png, pdf 파일 첨부가 가능합니다. <br>파일용량 1MB 이내 첨부가 가능합니다.</p>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="lt">이메일 *</div>
                                                        <div class="rt"><input type="email" id="TAX_BILL_EMAIL" name="TAX_BILL_EMAIL"></div>
                                                    </li>
                                                </ul>
                                            </li>

                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="btn_wrap">
                        <!-- 임시로 주문완료 페이지로 링크 연결해놓음 -->
                        <button type="button" id="orderNextBtn" class="black_btn shadow_btn w_500" onclick="order_payment()">결제하기</button>
                    </div>
                </section>
            </form>
        </div>
    </main>

    <?php
        if ($TYPE_MONITOR == "MO") {
            include_once $_SERVER['DOCUMENT_ROOT'] . '/php/temp/INIS/INIstdpay_temp_mo.php'; 
        } else {
            include_once $_SERVER['DOCUMENT_ROOT'] . '/php/temp/INIS/INIstdpay_temp.php'; 
        }
    ?>

    <!-- 하단 푸터 -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/footer.php" ?>
</body>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . artFoldName . "/include/bottom.php"; ?>

<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    $("input[name='orderPayType']").trigger("change");
    $("input[name='issuance']").trigger("change");
    $("input[name='cashReceipt']").trigger("change");

    function execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다.
                let addr = ''; // 주소 변수

                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    addr = data.roadAddress;
                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    addr = data.jibunAddress;
                }

                $('#DLVY_ADDRESS_ZIPCODE').val(data.zonecode);
                $('#DLVY_ADDRESS').val(addr);
            }
        }).open();
    }

    function ordererChange(checkbox) { // 주문자 정보와 동일
        let isChecked = checkbox.checked;

        if (isChecked) { // 체크되었을 때의 동작
            $("#DLVY_NAME").val($("#order_name").val());
            $("#DLVY_MOBILE").val($("#order_tel").val());
        } else { // 체크되지 않았을 때의 동작
            $("#DLVY_NAME").val("");
            $("#DLVY_MOBILE").val("");
        }
    }

    $(document).ready(function() {
        // 약관 전체 동의
        $("[name=agreeChkAll]").click(function() {
            allCheckFunc(this);
        });

        $("[name*='agreeChk']:not('#agreeChkAll')").each(function() {
            $(this).click(function() {
                oneCheckFunc($(this));
            });
        });

        // 결제수단 선택
        $(".payment_info2").hide();
        $(".payment_info3").hide();

        window.onpageshow = function (event) {
            // 새로고침: window.performance.navigation.type == 1
            // 뒤로가기: window.performance.navigation.type == 2
            if (event.persisted || (window.performance && (window.performance.navigation.type == 1 || window.performance.navigation.type == 2))) {

                // 현재 브라우저에서 WebStorage를 지원할 때
                if (('sessionStorage' in window) && window['sessionStorage'] !== null) {

                    $(".remember_data .form_label_ani input").each(function(index, item){
                        if ($(this).val().trim() == '') {
                            $(this).removeClass("focus");
                            $(this).siblings("label").removeClass("label_float");
                            $(this).siblings(".erase_btn").removeClass("erase_float");
                            return false;
                        } else {
                            $(this).addClass("focus");
                            $(this).siblings("label").addClass("label_float");
                            $(this).siblings(".erase_btn").addClass("erase_float");
                        }
                    });

                    // 결제수단 데이터 기억
                    var orderPayType = $("input[name='orderPayType']:radio:checked").val();
                    let hiddenInput = "";

                    <?php if ($TYPE_MONITOR == "MO") { ?>
                        hiddenInput = document.querySelector('input[name="P_INI_PAYMENT"]');

                        if (orderPayType == 'payCard') { // 신용카드 결제
                            $(".payment_info1").show();
                            $(".payment_info2").hide();
                            $(".payment_info3").hide();
                            hiddenInput.value = 'CARD';
                        } else if (orderPayType == 'payAccount') { // 실시간 계좌이체
                            $(".payment_info1").hide();
                            $(".payment_info2").show();
                            $(".payment_info3").hide();
                            hiddenInput.value = 'VBANK';
                        } else if (orderPayType == 'payNoBankbook') { // 무통장 입금
                            $(".payment_info1").hide();
                            $(".payment_info2").hide();
                            $(".payment_info3").show();
                            hiddenInput.value = '';
                        }
                    <?php } else { ?>
                        hiddenInput = document.querySelector('input[name="gopaymethod"]');

                        if (orderPayType == 'payCard') { // 신용카드 결제
                            $(".payment_info1").show();
                            $(".payment_info2").hide();
                            $(".payment_info3").hide();
                            hiddenInput.value = 'Card';
                        } else if (orderPayType == 'payAccount') { // 실시간 계좌이체
                            $(".payment_info1").hide();
                            $(".payment_info2").show();
                            $(".payment_info3").hide();
                            hiddenInput.value = 'Directbank';
                        } else if (orderPayType == 'payNoBankbook') { // 무통장 입금
                            $(".payment_info1").hide();
                            $(".payment_info2").hide();
                            $(".payment_info3").show();
                            hiddenInput.value = '';
                        }
                    <?php }?>

                    // 무통장입금 - 현금영수증 데이터 기억
                    var issuanceType = $("input[name='issuance']:radio:checked").val();

                    if (issuanceType == 'issuanceNo') { // 발급안함
                        $(".chk_cash").hide();
                        $(".chk_tax").hide();
                    } else if (issuanceType == 'issuanceCash') { // 현금영수증
                        $(".chk_cash").show();
                        $(".chk_tax").hide();
                    }

                    // 무통장 입금 > 현금영수증 선택 후
                    $(".chk_cash_type2").hide();
                    var cashReceiptType = $("input[name='cashReceipt']:radio:checked").val();

                    if (cashReceiptType == 'cashReceipt1') { // 개인 소득공제
                        $(".chk_cash_type1").show();
                        $(".chk_cash_type2").hide();
                    } else if (cashReceiptType == 'cashReceipt2') { // 사업자 지출증빙
                        $(".chk_cash_type1").hide();
                        $(".chk_cash_type2").show();
                    }
                }
            }
        }

        $("input[name='orderPayType']:radio").change(function() {
            var orderPayType = this.value;
            let hiddenInput = "";

            <?php if ($TYPE_MONITOR == "MO") { ?>
                hiddenInput = document.querySelector('input[name="P_INI_PAYMENT"]');

                if (orderPayType == 'payCard') { // 신용카드 결제
                    $(".payment_info1").show();
                    $(".payment_info2").hide();
                    $(".payment_info3").hide();
                    hiddenInput.value = 'CARD';
                } else if (orderPayType == 'payAccount') { // 실시간 계좌이체
                    $(".payment_info1").hide();
                    $(".payment_info2").show();
                    $(".payment_info3").hide();
                    hiddenInput.value = 'VBANK';
                } else if (orderPayType == 'payNoBankbook') { // 무통장 입금
                    $(".payment_info1").hide();
                    $(".payment_info2").hide();
                    $(".payment_info3").show();
                    hiddenInput.value = '';
                }
            <?php } else { ?>
                hiddenInput = document.querySelector('input[name="gopaymethod"]');

                if (orderPayType == 'payCard') { // 신용카드 결제
                    $(".payment_info1").show();
                    $(".payment_info2").hide();
                    $(".payment_info3").hide();
                    hiddenInput.value = 'Card';
                } else if (orderPayType == 'payAccount') { // 실시간 계좌이체
                    $(".payment_info1").hide();
                    $(".payment_info2").show();
                    $(".payment_info3").hide();
                    hiddenInput.value = 'Directbank';
                } else if (orderPayType == 'payNoBankbook') { // 무통장 입금
                    $(".payment_info1").hide();
                    $(".payment_info2").hide();
                    $(".payment_info3").show();
                    hiddenInput.value = '';
                }
            <?php }?>
        });

        // 무통장 입금 선택 후
        $("input[name='issuance']:radio").change(function() {
            var issuanceType = this.value;

            if (issuanceType == 'issuanceNo') { // 발급안함
                $(".chk_cash").hide();
                $(".chk_tax").hide();
            } else if (issuanceType == 'issuanceCash') { // 현금영수증
                $(".chk_cash").show();
                $(".chk_tax").hide();
            } else if (issuanceType == 'issuanceTax') { // 세금계산서
                $(".chk_cash").hide();
                $(".chk_tax").show();
            }
        });

        // 무통장 입금 > 현금영수증 선택 후
        $(".chk_cash_type2").hide();
        $("input[name='cashReceipt']:radio").change(function() {
            var cashReceiptType = this.value;

            if (cashReceiptType == 'cashReceipt1') { // 개인 소득공제
                $(".chk_cash_type1").show();
                $(".chk_cash_type2").hide();
            } else if (cashReceiptType == 'cashReceipt2') { // 사업자 지출증빙
                $(".chk_cash_type1").hide();
                $(".chk_cash_type2").show();
            }
        });

    });

    // 약관 전체 동의
    function allCheckFunc(obj) {
        $("[name*='agreeChk']:not('#agreeChkAll')").prop("checked", $(obj).prop("checked"));
    }

    // 체크박스 체크시 전체선택 체크 여부
    function oneCheckFunc(obj) {
        var allObj = $("[name=agreeChkAll]");
        var objName = $(obj).attr("name");

        if ($(obj).prop("checked")) {
            checkBoxLength = $("[name*='agreeChk']:not('#agreeChkAll')").length;
            checkedLength = $("[name*='agreeChk']:not('#agreeChkAll'):checked").length;

            if (checkBoxLength == checkedLength ) {
                allObj.prop("checked", true);
            } else {
                allObj.prop("checked", false);
            }
        } else {
            allObj.prop("checked", false);
        }
    }

    // 약관 팝업
    function onAgreePop1() {
        $(".popup_bg").stop().fadeIn();
        $("#agreePop1").stop().fadeIn();
        $('html, body').addClass("noScroll");
    }

    function onAgreePop2() {
        $(".popup_bg").stop().fadeIn();
        $("#agreePop2").stop().fadeIn();
        $('html, body').addClass("noScroll");
    }

    let gfnfile = [];

    //파일업로드
    function fileupload(obj, id, strExt, limitSize) {
        gfnfile = {
              mode : 'O' // [I : 이미지 , M : 다중 업로드, O : 한개만 업로드시]
            , obj  : obj // input type가 file인 값 ex) 보통은 this로 값 넣어주면됨
            , id   : id // 파일업로드의 값이 들어가는 위치 ex) #file_list / #preview_
            , strExt : strExt // 확장자 ex) jpg|gif|jpeg|png|pdf|zip
            , limitSize : limitSize // 파일의 사이즈를 확인
            , fileMap : '' // mode가 M인경우 다중파일일 경우 값 저장을 위하여
            , formData_del : '' // mode가 M인경우 다중파일 삭제 기능을 사용하기위해서
            , del_count : ''// mode가 M인경우 삭제 pk값보관
            , file_list_row : '' // mode가 M인경우 다중파일의 pk값을 보관
            , row_val : ''//mode가 M인경우  다중파일의  max값을 지정해줌
            , ues : 'M'// 관리자 / 메인페이지 확인 'A' : 관리자 / 'M' : 메인페이지 및 커스텀
        };

        gfn_changeFile(gfnfile);
    }

    function order_payment() {
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("결제를 진행하시겠습니까?")) {
            if ($('input[name="orderPayType"]:checked').val() == "payNoBankbook") { // 무통장 입금
                $("#frm").submit();
            } else {

                let paymentInfo = {
                      buyername: $("#order_name").val()
                    , buyertel: $("#order_tel").val()
                    , buyeremail: $("#order_email").val()
                };

                <?php if ($TYPE_MONITOR == "MO") { ?>
                    INISTVALUEINFO_MO(paymentInfo);
                <?php } else { ?>
                    INISTVALUEINFO(paymentInfo);
                <?php }?>

                //alert("오픈 예정");
                let formData = new FormData($("#frm")[0]);
                formData.set("mode", 'INISTPAY_INFO');

                $.ajax({
                    url: "/php/ajax_module.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        let json = JSON.parse(data);

                        if (json.code == 200) {
                            <?php if ($TYPE_MONITOR == "MO") { ?>
                                on_pay();
                            <?php } else { ?>
                                paybtn();
                            <?php }?>
                        }
                    },
                    beforeSend: function() {
                        $(".wrap-loading").removeClass("display-none");
                    },
                    complete: function() {
                        $(".wrap-loading").addClass("display-none");
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
                });
            }
        }
    }

    /**
     * name : ufn_validation
     * comment : 유효성 체크
     */
    function ufn_validation() {
        if ($("#order_name").val() == "") {
            alert("주문자 정보 입력\n[이름을 입력해주세요.]");
            $("#order_name").focus();
            return false;
        }

        if ($("#order_tel").val() == "") {
            alert("주문자 정보 입력\n[연락처를 입력해주세요.]");
            $("#order_tel").focus();
            return false;
        }

        if ($("#order_email").val() == "") {
            alert("주문자 정보 입력\n[이메일을 입력해주세요.]");
            $("#order_email").focus();
            return false;
        }

        if ($("#DLVY_NAME").val() == "") {
            alert("배송정보 입력\n[이름을 입력해주세요.]");
            $("#DLVY_NAME").focus();
            return false;
        }

        if ($("#DLVY_MOBILE").val() == "") {
            alert("배송정보 입력\n[연락처를 입력해주세요.]");
            $("#DLVY_MOBILE").focus();
            return false;
        }

        if ($("#DLVY_ADDRESS_ZIPCODE").val() == "") {
            alert("배송정보 입력\n[주소를 선택해주세요.]");
            $("#DLVY_ADDRESS_ZIPCODE").focus();
            return false;
        }

        if (!$("#agreeChk1").is(":checked")) {
            alert("주식회사 글린트의 정책 및 이용약관에 체크해 주세요.");
            $("#agreeChk1").focus();
            return false;
        }

        if (!$('input[name="orderPayType"]:checked').val()) {
            alert("결제 방식 선택\n[결제 방식을 선택해주세요.]");
            $('input[name="orderPayType"]').first().focus();
            return false;
        }

        if ($('input[name="orderPayType"]:checked').val() == "payNoBankbook") { // 무통장 입금 
            if ($("#NO_BANK_CD").val() == "") {
                alert("입금은행을 선택해주세요.");
                $("#NO_BANK_CD").focus();
                return false;
            }

            if ($("#NO_BANK_DEPOSITOR").val() == "") {
                alert("입금자 명을 입력해주세요.");
                $("#NO_BANK_DEPOSITOR").focus();
                return false;
            }

            if ($('input[name="issuance"]:checked').val() == "issuanceCash") { // 무통장 입금 - 현금영수증 [개인 소득공제]
                if ($('input[name="cashReceipt"]:checked').val() == "cashReceipt1") {
                    if ($("#CASH_MOBILE").val() == "") {
                        alert("개인 소득공제\n[연락처를 입력해주세요.]");
                        $("#CASH_MOBILE").focus();
                        return false;
                    }

                    if ($("#CASH_EMAIL").val() == "") {
                        alert("개인 소득공제\n[이메일을 입력해주세요.]");
                        $("#CASH_EMAIL").focus();
                        return false;
                    }

                } else if ($('input[name="cashReceipt"]:checked').val() == "cashReceipt2") { // 무통장 입금 - 현금영수증 [사업자 지출증빙]
                    if ($("#CASH_BUSINESS").val() == "") {
                        alert("사업자 지출증빙\n[사업자번호를 입력해주세요.]");
                        $("#CASH_BUSINESS").focus();
                        return false;
                    }

                    if ($("#CASH_EMAIL2").val() == "") {
                        alert("사업자 지출증빙\n[이메일을 입력해주세요.]");
                        $("#CASH_EMAIL2").focus();
                        return false;
                    }
                }
            } else if ($('input[name="issuance"]:checked').val() == "issuanceTax") { // 무통장 입금 - 세금계산서
                if ($("#ATTACH").val() == "") {
                    alert("세금계산서\n[사업자등록증을 등록해주세요.]");
                    $("#ATTACH").focus();
                    return false;
                }

                if ($("#TAX_BILL_EMAIL").val() == "") {
                    alert("세금계산서\n[이메일을 입력해주세요.]");
                    $("#TAX_BILL_EMAIL").focus();
                    return false;
                }
            }

        } else if ($('input[name="orderPayType"]:checked').val() == "payCard") { // 신용카드 결제
            //alert("준비중입니다.");
            //return false;
        } else if ($('input[name="orderPayType"]:checked').val() == "payAccount") { // 실시간 계좌이체
            //alert("준비중입니다.");
            //return false;
        }

        return true;
    }
</script>