
/**
 * name : gfn_isNull
 * comment : null 확인
 */
function gfn_isNull(value) {
    if (value === null || typeof value === "undefined" || value === "" || value === "undefined" || value === undefined) {
        return true;
    }

    return false;
}

/**
 * name : gfn_nvlChange
 * comment : null 값 변경 
 */
function gfn_nvlChange(chk_val, change_val) {
    let value = "";

    if (gfn_isNull(chk_val)) {
        if (!gfn_isNull(change_val)) {
            value = change_val;
        }
    } else {
        value = chk_val;
    }

    return value;
}

/**
 * name : gfn_changeFile
 * comment : 파일 업로드 공통
 *          mode : [I : 이미지 , M : 다중 업로드, O : 한개만 업로드시]
 *          obj : input type가 file인 값 ex) 보통은 this로 값 넣어주면됨
 *          id : 파일업로드의 값이 들어가는 위치 ex) #file_list / #preview_
 *          strExt : 확장자 ex) jpg|gif|jpeg|png|pdf|zip
 *          limitSize : 파일의 사이즈를 확인
 *          options : 다중 파일업로드의 데이터 보관
 *              fileMap : mode가 M인경우 다중파일일 경우 값 저장을 위하여
 *              formData_del : mode가 M인경우 다중파일 삭제 기능을 사용하기위해서
 *              del_count :  mode가 M인경우 삭제 pk값보관
 *              file_list_row : mode가 M인경우 다중 업로드의 row 및 줄을 만들기위해 사용 ex) file-row
 *              row_val : mode가 M인경우 다중 업로드의 파일 올릴 max값을 지정해줌
 *              ues : 관리자 / 메인페이지 확인 'A' : 관리자 / 'M' : 메인페이지 및 커스텀
 */
function gfn_changeFile(options) {
    let {
          mode // [I : 이미지 , M : 다중 업로드, O : 한개만 업로드시]
        , obj // input type가 file인 값 ex) 보통은 this로 값 넣어주면됨
        , id // 파일업로드의 값이 들어가는 위치 ex) #file_list / #preview_
        , strExt // 확장자 ex) jpg|gif|jpeg|png|pdf|zip
        , limitSize // 파일의 사이즈를 확인
        , fileMap // mode가 M인경우 다중파일일 경우 값 저장을 위하여
        , formData_del // mode가 M인경우 다중파일 삭제 기능을 사용하기위해서
        , del_count // mode가 M인경우 삭제 pk값보관
        , file_list_row // mode가 M인경우 다중파일의 pk값을 보관
        , row_val //mode가 M인경우  다중파일의  max값을 지정해줌
        , ues // 관리자 / 메인페이지 확인 'A' : 관리자 / 'M' : 메인페이지 및 커스텀
    } = options;
    
    if (gfn_isNull(strExt)) { // 기본셋팅 값 추가
        strExt = "jpg|jpeg|png|gif|pdf|doc|docx|ppt|pptx|xls|xlsx|zip";
    }

    let allowed_ext = strExt.split("|");
    let str_allowed_ext = allowed_ext.join(", ");
    let max_size = limitSize * 1024 * 1024;
    let arrImgFiles = obj.files;
    let file_row = 0;
    let img_size = arrImgFiles.length;
    let map_size = fileMap.size;

    if (gfn_isNull(file_list_row)) {
        file_list_row = '.file-row';
    }
    
    let file_list_row_name = file_list_row.substring(1);

    if (mode == "M") {
        file_row = parseInt($(file_list_row).length);

        if (!gfn_isNull(row_val)) {
            if ((map_size + img_size) > row_val || file_row >= row_val) {
                alert("첨부파일은 " + row_val + "개까지 업로드 가능합니다.");
                return 0;
            }
        }
    }

    $.each(arrImgFiles, function (index, file) {
        let file_name = file.name;
        let file_size = file.size;
        let ext = file.name.split(".").pop().toLowerCase();
        let idx = 0;
        let html = "";

        if (allowed_ext.length > 0) {
            if ($.inArray(ext, allowed_ext) == -1) {
                alert("첨부 파일은 " + str_allowed_ext + " 확장자만 가능합니다.");
                obj.focus();
                obj.value = "";

                if (mode == "I") {
                    $(id).prop("src", "");
                }

                return 0;
            }
        }

        if (!check_special_str(file_name)) {
            alert("파일명에 특수문자가 포함되어 있습니다.");
            obj.focus();
            obj.value = "";

            return 0;
        }

        if (file_size > max_size) {
            alert("파일용량은 " + limitSize + "MB 까지 가능합니다.");
            obj.focus();
            obj.value = "";

            if (mode == "I") {
                $(id).prop("src", "");
            }

            return 0;
        }

        if (mode == "M") {
            if (file_row > 0) {
                idx = parseInt($(file_list_row).last().data("idx")) + 1;
            } else {
                idx = index;
            }
            
            if (ues == "A") {
                html += "<div class=\"input-group file-row\" style = \"padding : 5px 0px; column-gap: 10px;\" id=\"" + file_list_row_name + idx + "\" data-idx=\"" + idx + "\"> \n";
                html += "   <img style='height:16px;' src=/adm/img/paper-clip.svg' alt='paper clip'> \n";
                html += "   <p>" + file_name + "</p>\n";
                html += "   <a href=\"javascript:void(0);\" class=\"text-decoration-none font-weight-bold text-dark\" onclick=\"javascript:upFileDel(" + idx + ",'" + file_list_row_name + "');\">\n";
                html += "       x\n";
                html += "   </a> \n";
                html += "</div>\n";
            } 
            else if (ues == "M") {
                html = `<li class="file_list" id="${file_list_row_name + idx}" data-idx=${idx}>
                            <p class="name">${file_name}</p>
                            <button type="button" onclick="upFileDel(${idx});">
                                <img src="/shop/img/erase_icon.png" alt="">
                            </button>
                        </li>`;
            }

            $(id).append(html);

            fileMap.set(idx, file);
        } else if (mode == "O") {
            $(id).html("");

            if (ues == "A") {
                html += "<div style = 'margin: 5px 0 0 0;'>\n";
                html += "   <img style='height:16px;' src='/adm/img/paper-clip.svg' alt='paper clip'> \n";
                html += "   <p style='display:inline-block'>" + file_name + "</p> \n";
                html += "</div> \n";
            } else {
                
            }
    
            $(id).html(html);
        } else if (mode == "I") {
			var reader = new FileReader();
			
            reader.onload = function (e) {
                $(id).attr('src', e.target.result);
            }
            
            reader.readAsDataURL(file);
        }
    });

    return {fileMap: fileMap, formData_del: formData_del, del_count: del_count};
}

/**
 * name : gfn_upFileDel
 * comment : 파일 업로드 삭제
 *          idx : 삭제되는 값의 PK값
 *          options : 다중 파일업로드의 데이터 보관
 *              fileMap : mode가 M인경우 다중파일일 경우 값 저장을 위하여
 *              formData_del : mode가 M인경우 다중파일 삭제 기능을 사용하기위해서
 *              del_count :  mode가 M인경우 삭제 pk값보관
 */
function gfn_upFileDel(idx, options) {
    if (confirm("해당 첨부파일을 삭제하시겠습니까?")) {
        let {
            fileMap // mode가 M인경우 다중파일일 경우 값 저장을 위하여
            , formData_del // mode가 M인경우 다중파일 삭제 기능을 사용하기위해서
            , del_count // mode가 M인경우 삭제 pk값보관
            , file_list_row // mode가 M인경우 다중파일의 pk값을 보관
        } = options

        let chk = 'N';

        fileMap.delete(idx);
        $(file_list_row + idx).remove();
        $(".custom-file-label").eq(1).text("업로드된 파일 " + fileMap.size + "개");

        for (let i = 0; i < formData_del.length; i++) {
            if (idx == formData_del[i]) {
                chk = 'Y';
                break;
            }
        }

        if (chk == 'N') {
            formData_del[del_count++] = idx;
        }

        return {fileMap: fileMap, formData_del: formData_del, del_count: del_count};
    }
}

/**
 * name : validatePassword
 * comment : 비밀번호에 영문, 숫자, 특수문자 중 requiredCriteria 이상의 조합을 포함하여 requiredLength 자 이상인지 확인
 *          password : 확인할 값
 *          requiredCriteria : 3가의 이상의 조합을 포함 영문, 숫자, 특수문자
 *          requiredLength : 최소 길이값
 */
function validatePassword(password, requiredCriteria, requiredLength) {
    // 비밀번호에 영문, 숫자, 특수문자 중 requiredCriteria 이상의 조합을 포함하여 requiredLength 자 이상인지 확인
    var hasAlpha = /[a-zA-Z]/.test(password);
    var hasNumber = /\d/.test(password);
    var hasSpecial = /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/.test(password);

    // 조건에 맞는 조합의 개수를 계산
    var matchingCriteria = (hasAlpha ? 1 : 0) + (hasNumber ? 1 : 0) + (hasSpecial ? 1 : 0);

    return matchingCriteria >= requiredCriteria && password.length >= requiredLength;
}

//특수문자 체크
function check_special_str(str) {
    var pattern =  /[\{\}\/?,;:|*~`!^\+<>@\#$%&\\\=\'\"]/gi;

    if (pattern.test(str)) {
        return false;
    }

    return true;
}

// 이니시스 정보 작성
function INISTVALUEINFO(INFO_VALUE) {
    $('input[name="buyername"]').val(INFO_VALUE['buyername']); //주문자명
    $('input[name="buyertel"]').val(INFO_VALUE['buyertel']); // 주문자 연락처
    $('input[name="buyeremail"]').val(INFO_VALUE['buyeremail']); // 주문자 이메일
}

function INISTVALUEINFO_MO(INFO_VALUE) {
    $('input[name="P_UNAME"]').val(INFO_VALUE['buyername']); //주문자명
    $('input[name="P_MOBILE"]').val(INFO_VALUE['buyertel']); // 주문자 연락처
    $('input[name="P_EMAIL"]').val(INFO_VALUE['buyeremail']); // 주문자 이메일
}

// 숫자에 콤마 추가하는 함수
function formatNumber(input, maxLength) {
    let value = input.value.replace(/[^\d]/g, ""); // 숫자 이외의 문자 제거

    let sliceValue = "";

    if (!gfn_isNull(maxLength) && maxLength != 0) {
        if (value > maxLength) {
            sliceValue = value.slice(0, maxLength);
        } else {
            sliceValue = value;    
        }
    } else {
        sliceValue = value;
    }
    
    let formattedValue = Number(sliceValue).toLocaleString(); // 숫자에 콤마 추가
    input.value = formattedValue
}

function gfn_alert(type, content, button1, button2, callback1, callback2) {
    let id = "";
    let e_url = "";
    let e_content = "";
    let e_button1 = "";
    let e_button2 = "";
    let e_callback1 = "";
    let e_callback2 = "";

    if (gfn_isNull(type)) {
        return;
    }

    if (!gfn_isNull(content)) {
        e_content = content;
    }

    if (!gfn_isNull(button1)) {
        e_button1 = button1;
    } else {
        e_button1 = "확인";
    }

    if (!gfn_isNull(button2)) {
        e_button2 = button2;
    } else {
        e_button2 = "취소";
    }

    if (!gfn_isNull(callback1)) {
        e_callback1 = callback1;
    } else {
        e_callback1 = 'cancel';
    }

    if (!gfn_isNull(callback2)) {
        e_callback2 = callback2;
    } else {
        e_callback2 = 'cancel';
    }

    if (type == "alert") {
        id = "alert_popup";
        e_url = "/include/alert_popup.php";

    } else if (type == "confirm") {
        id = "confirm_popup";
        e_url = "/include/confirm_popup.php";
    } else {
        return;
    }

    $.ajax({
        url: e_url, // HTML 파일 경로를 지정합니다.
        type: 'GET',
        success: function(data) {
            data = data.replace('${content}', e_content)
                       .replace('${button1}', e_button1)
                       .replace('${button2}', e_button2)
                       .replace('${callback1}', e_callback1)
                       .replace('${callback2}', e_callback2)
                       .replace(/\${id}/g, id);

            let parser = new DOMParser();
            let doc = parser.parseFromString(data, 'text/html');
            let popupDiv = doc.getElementById(id);

            document.body.appendChild(popupDiv);

            $(".popup_bg").stop().fadeIn();
            $("#" + id).stop().fadeIn();  
            $('html, body').addClass("noScroll");
        },
        error: function(xhr, status, error) {
            // 오류 처리
            console.error("팝업 파일을 불러오는 중 오류가 발생했습니다.");
        }
    });
}


// 팝업 닫기
function cust_popClose (callback, call_id) {
    $(".popup_bg").stop().fadeOut();
    $(".popup").stop().fadeOut();
    $('html, body').removeClass("noScroll");

    if (!gfn_isNull(callback)) {
        if (callback != 'cancel') {
            window[callback]();
        }
    }

    let popupDiv = document.getElementById(call_id);
    popupDiv.parentNode.removeChild(popupDiv);
}

// 이메일 검증
function isValidEmail(email) {
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    return emailPattern.test(email);
}

// 휴대폰 번호 유효성 검사
function isValidPhoneNumber(phoneNumber) {
    // 휴대폰 번호는 다양한 형식이 있을 수 있으므로 정확한 패턴을 지정하세요.
    // 예: 010-1234-5678 또는 01012345678 등
    const phonePattern = /^(01[0-9]{1})?[-]?[0-9]{4}[-]?[0-9]{4}$/;
    return phonePattern.test(phoneNumber);
}

// 숫자만 입력 가능하도록 처리하는 함수
function allowOnlyNumbers(input) {
    input.value = input.value.replace(/[^\d]/g, '');
}


/**
 * name : gfn_setupSelectBox
 * comment : 콤보박스 연관 설정 [필터링 기능]
 *          typeSelectBoxId : 첫번째 콤보박스 ID 값
 *          productSelectBoxId : 두번쨰 콤보박스 ID 값
 *          countryValue : 조건값 [첫번째에 해당하는 콤보박스의 값을 필터링후 두번쨰 콤보박스에서 찾을값]
 *          ex): gfn_setupSelectBox('selectBox ID', 'selectBox2 ID', COUNTRY_value);
 */
function gfn_setupSelectBox(typeSelectBoxId, productSelectBoxId, countryValue) {
    const selectBox = document.getElementById(typeSelectBoxId); // 첫번째 콤보박스
    const selectBox2 = document.getElementById(productSelectBoxId); // 두번째 콤보박스
    const options = selectBox2.options;

    selectBox.addEventListener('change', () => {
        const selectedValue = selectBox.value;

        Array.from(options).forEach(option => option.hidden = true); // 모든 option 요소 숨기기

        if (selectedValue === '') { // 전체 옵션 선택한 경우
            selectBox2.selectedIndex = -1; // 선택된 option 요소 초기화
            Array.from(options).forEach(option => option.hidden = true); // 모든 option 요소 숨기기
            selectBox2.options[0].hidden = false;
            selectBox2.selectedIndex = 0;
        } else { // 일반적인 경우
            const filteredOptions = Array.from(options).filter(option => {
                const dataCode = option.getAttribute('data-code1');
                return dataCode === selectedValue;
            });

            filteredOptions.forEach(option => option.hidden = false);
            selectBox2.options[0].hidden = false;

            if (countryValue != '') {
                for (let i = 0; i < selectBox2.options.length; i++) {
                    if (selectBox2.options[i].value === countryValue) {
                        selectBox2.selectedIndex = i;
                        countryValue = ''; // 값을 초기화
                        break;
                    }
                }
            } else {
                selectBox2.selectedIndex = 0;
            }
        }
    });

    selectBox.dispatchEvent(new Event('change')); // change 이벤트 강제 발생
}
