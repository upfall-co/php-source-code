<?php
/**
 * 파일명 : memberManagement_details.php
 * 내용 : 시크릿코드관리 페이지
 * 최초작성날짜 : 2023/08/03
 * 최초작성자 : 김민성
 * ------------------------------------
 * name       date        comment
 * 김민성    2023/08/03    V1.0
 */

    //head
    define("SUB", "");
    include_once __DIR__ .'/../common/head.php';
    
    //php setting
    include_once $_SERVER['DOCUMENT_ROOT'].'/php/memberManagement_details_code.php';
?>

<body>
    <div id="wrapper">
        <?php
            include_once __DIR__ .'/../common/nav.php';
        ?>

            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5><strong>회원 정보</strong></h5>

                                <div class="ibox-tools">
                                    <a class="collapse-link" >
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="ibox-content custom_detail">
                                <form id="frm" method="post" action="/php/member.php" enctype="multipart/form-data">
                                    <input type="hidden" id="m_seq" name="m_seq" value="<?=$m_seq?>"/>
                                    <input type="hidden" id="mp_seq" name="mp_seq" value="<?=$mp_seq?>"/>
                                    <input type="hidden" id="mode" name="mode" value="<?=$mode?>"/> 
                                    <input type="hidden" id="page_type" name="page_type" value="<?=$page_type?>"/>
                                    <input type="hidden" id="SEQ" name="SEQ" value="<?=$SEQ?>"/>
                                    <input type="hidden" id="M_TYPE_CD" name="M_TYPE_CD" value="<?=$M_TYPE_CD?>"/>
                                    <input type="hidden" id="M_ID" name="M_ID" value="<?=$M_ID?>"/>
                                    <input type="hidden" id="M_NAME" name="M_NAME" value="<?=$M_NAME?>"/>
                                    <input type="hidden" id="M_MOBILE" name="M_MOBILE" value="<?=$M_MOBILE?>"/>
                                    <input type="hidden" id="M_start_date" name="M_start_date" value="<?=$M_start_date?>"/>
                                    <input type="hidden" id="M_end_date" name="M_end_date" value="<?=$M_end_date?>"/>

                                    <div class="row">
                                        <div class="col-sm-12 b-r">
                                        <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">회원구분</label>

                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" value="<?=$_db_TYPE_CD_NM?>" disabled>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">아이디</label>

                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" value="<?=$_db_ID?>" disabled>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">비밀번호</label>

                                                <div class="col-sm-4">
                                                    <input type="password" class="form-control" id="PASSWORD" name="PASSWORD" value="" maxlength="255">
                                                    <span class="form-text m-b-none text-navy">비밀번호는 영문, 숫자, 특수문자 중 2가지 이상의 조합을 포함하여 <br> 8자 이상으로 등록해주세요.</span>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">* 이름</label>

                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" id="NAME" name="NAME" value="<?=$_db_NAME?>" placeholder="이름을 입력해주세요" maxlength="100">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">* 휴대폰</label>

                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" id="MOBILE" name="MOBILE" value="<?=$_db_MOBILE?>" placeholder="휴대폰 번호를 입력해주세요" oninput="autoHyphen(this)" maxlength="13">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">* 이메일</label>

                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" id="EMAIL" name="EMAIL" value="<?=$_db_EMAIL?>" placeholder="이메일을 입력해주세요" maxlength="255">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row custom_address">
                                                <label class="col-sm-1 col-form-label text-right">주소</label>

                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" id="ADDRESS_ZIPCODE" name="ADDRESS_ZIPCODE" value="<?=$_db_ADDRESS_ZIPCODE?>" readonly>
                                                </div>

                                                <button type="button" id="addressBtn" class="btn btn-default" onclick="execDaumPostcode()">
                                                    <i class="fa fa-map-marker"></i>
                                                    주소검색
                                                </button>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">기본주소</label>

                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" id="ADDRESS" name="ADDRESS" value="<?=$_db_ADDRESS?>" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">상세주소</label>

                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" id="ADDRESSDETAIL" name="ADDRESSDETAIL" value="<?=$_db_ADDRESSDETAIL?>" maxlength="255">
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">IP</label>

                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" value="<?=$_db_reg_ip?>" disabled>
                                                </div>
                                            </div>

                                            <div class="hr-line-dashed"></div>
                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">SNS 가입여부</label>

                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" value="<?=$_db_TOKEN_STATUS?>" disabled>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-1 col-form-label text-right">SNS 가입경로 <br>(카카오, 네이버)</label>

                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control mt-2" value="<?=$_db_TOKEN_PROVIDERS?>" disabled>
                                                </div>
                                            </div>

                                            <?php if ($_db_TYPE_CD == 'BSM') { ?>
                                                <div class="hr-line-dashed"></div>

                                                <div class="form-group row">
                                                    <label class="col-sm-1 col-form-label text-right">사업자명</label>

                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control" id="BUSINESS_NAME" name="BUSINESS_NAME" value="<?=$_db_BUSINESS_NAME?>" maxlength="255">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-1 col-form-label text-right">사업자등록번호</label>

                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control" id="BUSINESS_NUMBER" name="BUSINESS_NUMBER" value="<?=$_db_BUSINESS_NUMBER?>" maxlength="255">
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <div class="hr-line-solid"></div>
                                            <div class="dv_Button" id="dv_Button" name="dv_Button">
                                                <button type="button" class="btn btn-lg btn-primary float-right w60 ml-1" onclick="javascript:mod();">수정</button>
                                                <button type="button" class="btn btn-lg btn-outline btn-secondary float-right w60" onclick="javascript:location.href='memberManagement_main.php?<?=$query_string?>';">취소</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                //footer
                include_once __DIR__ .'/../common/footer.php';
            ?>
        </div>
    </div>

<?php
    //top_btn
    include_once __DIR__ .'/../common/bottom.php';
?>
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    // 폼 핸드폰
    autoHyphen = (target) => {
        target.value = target.value
        .replace(/[^0-9]/g, '')
        .replace(/^(\d{2,3})(\d{3,4})(\d{4})$/, `$1-$2-$3`);
    }

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

                $('#ADDRESS_ZIPCODE').val(data.zonecode);
                $('#ADDRESS').val(addr);
            }
        }).open();
    }

    function mod() { // 수정
        if (!ufn_validation()) { //유효성
            return false;
        }

        if (confirm("회원정보를 변경하시겠습니까?")) {
            $("#frm").submit();
        }
    }


    /**
     * name : ufn_validation
     * comment : 유효성 체크
     */
    function ufn_validation() {
        if ($("#PASSWORD" ).val() !== "") {
            var password = $("#PASSWORD" ).val();

            if (!validatePassword(password, 2, 8)) {
                alert("비밀번호는 영문, 숫자, 특수문자 중 2가지 이상의 조합을\n포함하여 8자 이상으로 등록해야 합니다.");
                $("#PASSWORD").focus();
                return false
            } 
        }

        if ($("#NAME" ).val() === "") {
            alert("이름을 입력해주세요.");
            $("#NAME").focus();
            return false
        }

        if ($("#MOBILE" ).val() === "") {
            alert("연락처를 입력해주세요.");
            $("#MOBILE").focus();
            return false
        }

        if ($("#EMAIL" ).val() === "") {
            alert("이메일을 입력해주세요.");
            $("#EMAIL").focus();
            return false
        }

        return true;

    }

    function validatePassword(password, requiredCriteria, requiredLength) {
        // 비밀번호에 영문, 숫자, 특수문자 중 requiredCriteria 이상의 조합을 포함하여 requiredLength 자 이상인지 확인
        var hasAlpha = /[a-zA-Z]/.test(password);
        var hasNumber = /\d/.test(password);
        var hasSpecial = /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/.test(password);

        // 조건에 맞는 조합의 개수를 계산
        var matchingCriteria = (hasAlpha ? 1 : 0) + (hasNumber ? 1 : 0) + (hasSpecial ? 1 : 0);

        return matchingCriteria >= requiredCriteria && password.length >= requiredLength;
    }
</script>