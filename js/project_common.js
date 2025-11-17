/**
 * name :Order_Cancel
 * comment : 주문 취소
 */
function Order_Cancel(change_stage) {
    let totalcountArray = [];
    let pkArray = [];
    let seqArray = [];
    let codeArray = [];
    let stateArray = [];
    let priceArray = [];

    let M_obj = $("[name=prdChk]");

    let state_chk = "N";

    M_obj.each(function () {
        let O_obj = $("#" + this.id);

        if (O_obj.is(":checked")) {

            let totalcount = O_obj.parent("li").data('totalcount');
            let pk = O_obj.parent("li").data('pk');
            let seq = O_obj.parent("li").data('seq');
            let code = O_obj.parent("li").data('code');
            let state = O_obj.parent("li").data('state');
            let price = O_obj.parent("li").data('val');

            if (state == "42" || state == "52") {
                let op_name = O_obj.parent("li").parent("ul").find(".td_name").text();

                if (gfn_isNull(op_name)) {
                    op_name = O_obj.parent("li").parent("ul").find(".prdName").text();
                }

                alert('이미 취소된 내역이 존재합니다. [' + op_name + ']');
                state_chk = "Y";
                return;
            }

            if (totalcountArray.indexOf(totalcount) === -1) {
                totalcountArray.push(totalcount);
            }

            if (pkArray.indexOf(pk) === -1) {
                pkArray.push(pk);
            }

            seqArray.push(seq);
            codeArray.push(code);
            stateArray.push(state);
            priceArray.push(price);
        }
    });

    if (state_chk == "Y") {
        return;
    }

    if (seqArray.length == 0) {
        alert('주문내역을 선택해주세요.');
        return;
    }

    let SEQ = $("#SEQ").val();
    let TYPE_CD = $("#TYPE_CD").val();
    let STATE_CD = $("#STATE_CD").val();
    let INICIS_SEQ = $("#INICIS_SEQ").val();
    let MOBILE = $("#MOBILE").val();
    let TOTAL_NOW_PRICE = $("#TOTAL_NOW_PRICE").val();
    let REAL_DLVY_PRICE = $("#REAL_DLVY_PRICE").val();

    let totalcountString = totalcountArray.join(',');
    let pkString = pkArray.join(',');
    let seqString = seqArray.join(',');
    let codeString = codeArray.join(',');
    let stateString = stateArray.join(',');
    let priceString = priceArray.join(',');

    var list = {
          'mode': 'ORDERCANCEL'
        , 'SEQ': SEQ
        , 'TYPE_CD': TYPE_CD
        , 'STATE_CD': STATE_CD
        , 'INICIS_SEQ': INICIS_SEQ
        , 'MOBILE': MOBILE
        , 'TOTAL_NOW_PRICE': TOTAL_NOW_PRICE
        , 'REAL_DLVY_PRICE' : REAL_DLVY_PRICE
        , 'totalcount': totalcountString
        , 'pk': pkString
        , 'val': seqString
        , 'Options': codeString
        , 'state': stateString
        , 'price': priceString
        , 'change_stage': change_stage
    };

    let msg = "";

    if (change_stage == "51") {
        msg = "환불요청을 하시겠습니까?";
    } else {
        msg = "주문취소를 진행하시겠습니까?";
    }

    if (confirm(msg)) {
        $.ajax({
            type: "POST"
            , url: "/php/ajax_module.php"
            , data: list
            , success: function (data) {
                // 처리 성공 시 실행할 코드
                let json = JSON.parse(data);

                alert(json.msg);

                if (json.code == 200) {
                    location.reload();
                }
            }
            , error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
}

/**
 * name :countPlus
 * comment : 플러스버튼 클릭시 
 */
function countPlus(SEQ, menu) {
    let countEl = document.getElementById("optionCount" + SEQ);
    let count = document.getElementById("optionCount" + SEQ).value;

    count++;
    countEl.value = count;

    onchagePrice(SEQ, 'PLUS', menu, 1, 'BTN');
}

/**
 * name :countMinus
 * comment : 마이너스버튼 클릭시 
 */
function countMinus(SEQ, menu) {
    let countEl = document.getElementById("optionCount" + SEQ);
    let count = document.getElementById("optionCount" + SEQ).value;

    if (count > 1) {
        count--;
        countEl.value = count;

        onchagePrice(SEQ, 'MINUS', menu, 1, 'BTN');
    }
}

let countValue = new Array();

/**
 * name :countInput
 * comment : input text를 직접 변경시
 */
function countInput(SEQ, menu) {

    let countEl = document.getElementById("optionCount" + SEQ);
    let count = document.getElementById("optionCount" + SEQ).value;

    if (count == 0) {
        count = 1;
        countEl.value = count;
    }


    if (gfn_isNull(countValue[SEQ])) {
        countValue[SEQ] = 0;
    } 

    if (countValue[SEQ] < count) {
        onchagePrice(SEQ, 'PLUS', menu, count, 'EDT');
    } else {
        onchagePrice(SEQ, 'MINUS', menu, count, 'EDT');
    }

    countValue[SEQ] = count;
};

/**
 * name :onchagePrice
 * comment : 토탈 금액변경 [작품상세, 장바구니]
 */
function onchagePrice(SEQ, TYPE, menu, count, Element) {
    let countEl = document.getElementById(SEQ);

    const selectedOptionPrice = countEl.dataset['val'];
    const selectedOptionMPrice = countEl.dataset['mval'];

    let currentValue = "";
    let newValue = 0;

    let currentValue2 = "";
    let newValue2 = 0;

    if (TYPE == "PLUS") {
        if (Element == "BTN") {
            currentValue = parseInt($("#totalPrice_text").text().replace(/,/g, ''));
            newValue = currentValue + parseInt(selectedOptionPrice) + parseInt(selectedOptionMPrice);
    
            currentValue2 = parseInt($("#option_price" + SEQ).text().replace(/,/g, ''));
            newValue2 = currentValue2 + parseInt(selectedOptionPrice) + parseInt(selectedOptionMPrice);
        } else if (Element == "EDT") {
            currentValue = parseInt($("#totalPrice_text").text().replace(/,/g, ''));
            currentValue2 = parseInt($("#option_price" + SEQ).text().replace(/,/g, ''));

            newValue2 = (parseInt(selectedOptionPrice) + parseInt(selectedOptionMPrice)) * parseInt(count);
            newValue = (currentValue - currentValue2) + ((parseInt(selectedOptionPrice) + parseInt(selectedOptionMPrice)) * parseInt(count));
        }
    } else if (TYPE == "MINUS") {
        if (Element == "BTN") {
            currentValue = parseInt($("#totalPrice_text").text().replace(/,/g, ''));
            newValue = currentValue - (parseInt(selectedOptionPrice) + parseInt(selectedOptionMPrice));

            currentValue2 = parseInt($("#option_price" + SEQ).text().replace(/,/g, ''));
            newValue2 = currentValue2 - (parseInt(selectedOptionPrice) + parseInt(selectedOptionMPrice));
        } else if (Element == "EDT") {
            currentValue = parseInt($("#totalPrice_text").text().replace(/,/g, ''));
            currentValue2 = parseInt($("#option_price" + SEQ).text().replace(/,/g, ''));

            newValue2 = ((parseInt(selectedOptionPrice) + parseInt(selectedOptionMPrice)))  * parseInt(count);
            newValue = (currentValue - currentValue2) + ((parseInt(selectedOptionPrice) + parseInt(selectedOptionMPrice)) * parseInt(count));
        }
    }
    
    if (menu == "detail") {
        $("#totalPrice").val(newValue);
        $("#totalPrice_text").text(newValue.toLocaleString());

        $("#option_price" + SEQ).text(newValue2.toLocaleString());

        changeDELIVERY(menu);
    } else if (menu == "cart") {
        if ($('#' + SEQ).closest('.tbody').find("input[name=prdChk]").is(':checked')) {
            $("#totalPrice").val(newValue);
            $("#totalPrice_text").text(newValue.toLocaleString());

            $("#option_price" + SEQ).text(newValue2.toLocaleString());

            changeDELIVERY(menu);
        } else {
            $("#option_price" + SEQ).text(newValue2.toLocaleString());
        }
    }
}

/**
 * name :deleteOption
 * comment : 옵션삭제 [작품상세]
 */
function deleteOption(e, SEQ) {
    var optionValue = $(e).parent("li").data('code'); // 선택한 옵션의 value 값 가져오기
    var optionElement = document.querySelector('#shopSelect [value="' + optionValue + '"]'); // 선택한 옵션의 DOM 요소 가져오기

    // 삭제한 옵션을 다시 보이게 설정
    if (optionElement) {
        optionElement.style.display = 'block';
    }

    let currentValue = parseInt($("#totalPrice_text").text().replace(/,/g, ''));

    let currentValue2 = parseInt($("#option_price" + SEQ).text().replace(/,/g, ''));

    let newValue = currentValue - parseInt(currentValue2);

    $("#totalPrice").val(newValue);
    $("#totalPrice_text").text(newValue.toLocaleString());

    $(e).parent("li").remove();

    countValue[SEQ] = 0; // 해당변수 초기화

    changeDELIVERY('detail');
}

let previousDELIVERYPrice = 0;

/**
 * name :changeDELIVERY
 * comment : 배송비 금액 설정
 */
function changeDELIVERY(menu) {
    let DELIVERY_IF_PRICE = $("#DELIVERY_IF_PRICE").val();
    var currentValue = parseInt($("#totalPrice_text").text().replace(/,/g, ''));

    let newDELIVERYPrice = 0;

    let totalOptionPrice = 0; // "option_price"로 시작하는 아이디를 가진 요소의 합산값

    $("[id^='option_price']").each(function() {
        var optionPrice = parseInt($(this).text().replace(/,/g, ''));

        if (menu == "detail") {
            totalOptionPrice += optionPrice;
        } else if (menu == "cart") {
            if ($(this).closest('.tbody').find("input[name=prdChk]").is(':checked')) {
                totalOptionPrice += optionPrice;
            }
        }
    });

    if (totalOptionPrice >= DELIVERY_IF_PRICE) {
        newDELIVERYPrice = 0;
    } else {
        newDELIVERYPrice = $("#DELIVERY_PRICE").val();
    }

    if (newDELIVERYPrice != previousDELIVERYPrice) {
        newValue = currentValue - parseInt(previousDELIVERYPrice) + parseInt(newDELIVERYPrice);
        previousDELIVERYPrice = newDELIVERYPrice;
        $("#totalPrice").val(newValue);
        $("#totalPrice_text").text(newValue.toLocaleString());
    }

    if ($(".selected_option_list ul").length > 0) {
        if ($(".selected_option_list ul").children().length === 0) {
            $("#totalPrice").val(0);
            $("#totalPrice_text").text("0");
            previousDELIVERYPrice = 0;
        }
    }

    if ($("input[name=prdChk]").length > 0) {
        if ($("input[name=prdChk]:checked").length === 0) {
            $("#totalPrice").val(0);
            $("#totalPrice_text").text("0");
            previousDELIVERYPrice = 0;
        }
    }

    if (newDELIVERYPrice == 0) {
        $("#DELIVERY_PRICE_TEXT").text("무료");
    } else {
        $("#DELIVERY_PRICE_TEXT").text(parseInt(newDELIVERYPrice).toLocaleString());
    }
}
