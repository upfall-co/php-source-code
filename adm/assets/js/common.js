$(function () {
    //체크박스 전체 선택
    $(".checkbox-all").on("click", function () {
        if ($(".checkbox-all").is(":checked")) {
            $(".checkbox-one").prop("checked", true);
        } else {
            $(".checkbox-one").prop("checked", false);
        }
    });

    //체크박스 전체 선택 제어
    $(".checkbox-one").on("click", function () {
        if ($(".checkbox-one:checked").length == $(".checkbox-one").length) {
            $(".checkbox-all").prop("checked", true);
        } else {
            $(".checkbox-all").prop("checked", false);
        }
    });

    //파일 선택시 파일명 표출
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
});


$(document).ready(function () {
    // 파일 경로
    pathName = window.location.pathname;
    lPopUp();

});

//번호 하이푼 자동완성
function inputPhoneNumber(obj) {
    var number = obj.value.replace(/[^0-9]/g, "");
    var phone = "";

    if (number.length < 4) {
        return number;
    } else if (number.length < 7) {
        phone += number.substr(0, 3);
        phone += "-";
        phone += number.substr(3);
    } else if (number.length < 11) {
        phone += number.substr(0, 3);
        phone += "-";
        phone += number.substr(3, 3);
        phone += "-";
        phone += number.substr(6);
    } else {
        phone += number.substr(0, 3);
        phone += "-";
        phone += number.substr(3, 4);
        phone += "-";
        phone += number.substr(7);
    }

    obj.value = phone;
}

//NEW 번호 하이푼 자동완성
function inputPhoneNumber_v2(obj) {
    var number = obj.value.replace(/[^0-9]/g, "");
    var phone = "";

    phone = number.replace(/(^02|^0505|^1[0-9]{3}|^0[0-9]{2})([0-9]+)?([0-9]{4})$/,"$1-$2-$3").replace("--", "-");

    obj.value = phone;
}

//Ymd 포맷
function inputYmd(obj) {
    var number  = obj.value.replace(/[^0-9]/g, "");
    var ymd     = "";

    if (number.length < 4) {
        return number;
    } else if(number.length < 6) {
        ymd += number.substr(0, 4);
        ymd += "-";
        ymd += number.substr(4);
    } else {
        ymd += number.substr(0, 4);
        ymd += "-";
        ymd += number.substr(4, 2);
        ymd += "-";
        ymd += number.substr(6);
    }

    obj.value = ymd;
}

//사업자 번호 하이푼 자동 완성
function inputBsnRegNumber(obj) {
    var number  = obj.value.replace(/[^0-9]/g, "");
    var reg_num = '';

    if (number.length < 4) {
        return number;
    } else if (number.length < 6) {
        reg_num += number.substr(0, 3);
        reg_num += "-";
        reg_num += number.substr(3, 2);
    } else if (number.length < 11) {
        reg_num += number.substr(0, 3);
        reg_num += "-";
        reg_num += number.substr(3, 2);
        reg_num += "-";
        reg_num += number.substr(5);
    } else {
        reg_num += number.substr(0, 3);
        reg_num += "-";
        reg_num += number.substr(3, 2);
        reg_num += "-";
        reg_num += number.substr(5);
    }

    obj.value = reg_num;
}

//법인 번호 하이푼 자동 완성
function inputCorpRegNumber(obj) {
    var number  = obj.value.replace(/[^0-9]/g, "");
    var reg_num = '';

    if (number.length < 6) {
        return number;
    } else if (number.length < 12) {
        reg_num += number.substr(0, 6);
        reg_num += "-";
        reg_num += number.substr(6, 9);
    } else {
        reg_num += number.substr(0, 6);
        reg_num += "-";
        reg_num += number.substr(6, 9);
    }
    obj.value = reg_num;
}

//Date 포맷
function inputYYYYMMDD(obj) {
    var number      = obj.value.replace(/[^0-9]/g, "");
    var yyyymmdd    = "";

    if (number.length < 4) {
        return number;
    } else if (number.length < 6) {
        yyyymmdd += number.substr(0, 4);
        yyyymmdd += "-";
        yyyymmdd += number.substr(4);
    } else {
        yyyymmdd += number.substr(0, 4);
        yyyymmdd += "-";
        yyyymmdd += number.substr(4, 2);
        yyyymmdd += "-";
        yyyymmdd += number.substr(6);
    }

    obj.value = yyyymmdd;
}

//사업자 번호 유효성 검사
function validBsnRegNumber(value) {
    var valueMap = value.replace(/-/gi, '').split('').map(function(item) {
        return parseInt(item, 10);
    });

    if (valueMap.length === 10) {
        var multiply = new Array(1, 3, 7, 1, 3, 7, 1, 3, 5);
        var checkSum = 0;

        for (var i = 0; i < multiply.length; ++i) {
            checkSum += multiply[i] * valueMap[i];
        }

        checkSum += parseInt((multiply[8] * valueMap[8]) / 10, 10);
        return Math.floor(valueMap[9]) === (10 - (checkSum % 10));
    }

    return false;
}

//법인 번호 유효성 검사
function validCorpRegNumber(value) {
    var re  = /-/g;
    value   = value.replace('-', '');

    if (value.length != 13){
        return false;
    }

    var arr_regno       = value.split("");
    var arr_wt          = new Array(1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2);
    var iSum_regno      = 0;
    var iCheck_digit    = 0;

    for (i = 0; i < 12; i++) {
        iSum_regno += eval(arr_regno[i]) * eval(arr_wt[i]);
    }

    iCheck_digit = 10 - (iSum_regno % 10);
    iCheck_digit = iCheck_digit % 10;

    if (iCheck_digit != arr_regno[12]) {
        return false;
    }

    return true;
}

//이미지 사이즈 체크
function check_img_size(obj, width, height) {
    if (obj.width !== width || obj.height !== height) {
        alert(`이미지 가로 세로 비율이 맞지 않습니다.\n이미지 가로 ${width}px, 세로 ${height}px로 맞춰서 올려주세요.`);
    }
}

//이메일 체크
function check_email(str) {
    var reg_email = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;

    if (!reg_email.test(str)) {
        return false;
    }

    return true;
}

//전화번호, 핸드폰 번호 체크
function check_phone(str) {
    var reg_phone = /^\d{2,3}-\d{3,4}-\d{4}$/;

    if (!reg_phone.test(str)) {
        return false;
    }

    return true;
}

//사업자 번호 자리수 체크
function check_bsn_reg_num(str) {
    var reg_num = /^\d{3}-\d{2}-\d{5}$/;

    if (!reg_num.test(str)) {
        return false;
    }

    return true;
}

//법인 번호 자리수 체크
function check_corp_reg_num(str) {
    var reg_num = /^\d{6}-\d{7}$/;

    if (!reg_num.test(str)) {
        return false;
    }

    return true;
}

//아이디 체크
function check_id(str) {
    var reg_id = /^.*(?=.{4,20}$)(?=.*?[0-9])(?=.*[a-zA-Z]).*$/;

    if (!reg_id.test(str)) {
        return false;
    }

    return true;
}

//비밀번호 체크
function check_pw(str) {
    var reg_pw = /^.*(?=.{8,30}$)(?=.*\d)(?=.*[a-zA-Z])(?=.*[~!@#$%^&*()_+|<>?:{}]).*$/;

    if (!reg_pw.test(str)) {
        return false;
    }

    return true;
}

//비밀번호 체크2
function check_pw2(str) {
    var reg_pw = /^.*(?=.{5,30}$)(?=.*?[0-9])(?=.*[a-zA-Z]).*$/;

    if (!reg_pw.test(str)) {
        return false;
    }

    return true;
}

//영문, 숫자 체크
function check_eng_num(str) {
    var reg = /^.*(?=.*?[0-9])(?=.*[a-zA-Z]).*$/;

    if (!reg.test(str)) {
        return false;
    }

    return true;
}

//숫자만 체크
function check_num(str) {
    var reg_num = /^[0-9]*$/;

    if (!reg_num.test(str)) {
        return false;
    }

    return true;
}

//년도 체크
function check_yyyy(str) {
    var yyyy = parseInt(str);

    if (yyyy < 1900 || yyyy > 2100) {
        return false;
    }

    return true;
}

//날짜 유효성 체크 (윤달 포함)
function check_yyyymmdd(date) {
    var value_num = date.replace(/[^0-9]/g, "");

    //숫자를 제외한 나머지는 예외처리 합니다.
    //8자리가 아닌 경우 false
    if (value_num.length != 8) {
        alert("날짜를 2020-01-01 형식으로 입력해주세요.");
        return false;
    }

    //8자리의 yyyymmdd를 원본 , 4자리 , 2자리 , 2자리로 변경해 주기 위한 패턴생성을 합니다.
    var rxDatePattern   = /^(\d{4})(\d{1,2})(\d{1,2})$/;
    var dtArray         = value_num.match(rxDatePattern);

    if (dtArray == null) {
        return false;
    }

    //0번째는 원본, 1번째는 yyyy(년), 2번재는 mm(월), 3번재는 dd(일) 입니다.
    var dtYear  = dtArray[1];
    var dtMonth = dtArray[2];
    var dtDay   = dtArray[3];

    //yyyymmdd 체크
    if (dtYear < 1900 || dtYear > 2100) {
        alert("유효하지 않은 년도 했습니다.");
        return false;
    } else if (dtMonth < 1 || dtMonth > 12) {
        alert("유효하지 않은 월을 입력하셨습니다.");
        return false;
    } else if (dtDay < 1 || dtDay > 31) {
        alert("유효하지 않은 일을 입력하셨습니다.");
        return false;
    } else if ((dtMonth == 4 || dtMonth == 6 || dtMonth == 9 || dtMonth == 11) && dtDay == 31) {
        alert("유효하지 않은 일을 입력하셨습니다.");
        return false;
    } else if (dtMonth == 2) {
        var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
        if (dtDay > 29 || (dtDay == 29 && !isleap)) {
            alert("유효하지 않은 일을 입력하셨습니다.");
            return false;
        }
    }

    return true;
}


//특수문자 체크
function check_special_str(str) {
    var pattern =  /[\{\}\/?,;:|*~`!^\+<>@\#$%&\\\=\'\"]/gi;

    if (pattern.test(str)) {
        return false;
    }

    return true;
}

//파일 용량 정리
function fileSizePrint(data) {
    var size = "";

    if (data < 1024) size = data + "B";
    else if (data < 1024 * 1024) size = parseInt (data / 1024) + "KB";
    else if (data < 1024 * 1024 * 1024) size = parseInt (data / (1024 * 1024)) + "MB";
    else size = parseInt (data / (1024 * 1024 * 1024)) + "G";

    return size;
}

//파일 다운로드
function onDownload(url, name) {
    var a = document.createElement("a");
    a.href = url;
    a.download = name;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

//숫자 콤마 처리
function addComma(n) {
    if (isNaN(n)) {
        return 0;
    }
    var reg = /(^[+-]?\d+)(\d{3})/;
    n += '';
    while (reg.test(n)) {
        n = n.replace(reg, '$1' + ',' + '$2');
    }

    return n;
}

//textarea 사이즈 변경
function textareaResize(obj) {
    obj.style.height = "1px";
    obj.style.height = (12 + obj.scrollHeight) + "px";
}

//유튜브 url
function validateYouTubeUrl(url) {
    if (url !== undefined || url !== "") {
        let regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
        let match = url.match(regExp);
        if (match && match[2].length == 11) {
            let youtube_url = "https://www.youtube.com/embed/" + match[2];
            return youtube_url;
        } else {
            return false;
        }
    }
}

function lPopUp() {
    // 일반 (레이어 팝업 유형이 하나일 때)
    $(".l_pop_up__btn").on("click", function () {
      $(".layer_pop_up").stop().fadeIn(200).css("display", "flex");
    });
  
    // 일반 (레이어 팝업 유형이 여러개일 때)
    $(".l_pop_up__btn").on("click", function () {
      let layerData = $(this).data("layer");
  
      $(".layer_pop_up").stop().fadeIn(200).css("display", "flex");
      $(".l_pop_up__wrap").css("display", "none");
      $(".l_pop_up__wrap." + layerData).css("display", "flex");
  
      if (layerData === "c_delete") {
        let dataSeq = $(this).data("seq");
        
        $("#reg_comment_seq").val(dataSeq);
        $("#reg_comment_pw").val("");
      }
  
      $("html").css({
        overflow: "hidden",
      });
    });
  
    // 닫기 (닫기 버튼 클릭)
    $(".l_pop_up__close").on("click", function () {
      $(".layer_pop_up").stop().fadeOut(200);
      $("html").css({
        "overflow-y": "auto",
      });
    });
  
    // 닫기 (뒷배경 클릭)
    $(".l_pop_up__back").on("click", function () {
      $(".layer_pop_up").stop().fadeOut(200);
      $("html").css({
        "overflow-y": "auto",
      });
    });
  
  }