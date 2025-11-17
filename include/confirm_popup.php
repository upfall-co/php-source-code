<div id="confirm_popup" class="popup only_txt_popup confirm_popup">
    <div class="x_btn" onclick="cust_popClose('cancel', '${id}');"><img src="/img/icon/pop_x_btn.png" alt="닫기"></div>

    <div class="only_txt">
        <p>${content}</p>
    </div>

    <div class="pop_btn_wrap two_btn">
        <div class="black_btn" onclick="cust_popClose('${callback1}', '${id}')">${button1}</div>
        <div class="border_btn" onclick="cust_popClose('${callback2}', '${id}');">${button2}</div>
    </div>
</div>