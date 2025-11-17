<!-- common_code_modal_zone -->
<div id="common_code_modal_zone"></div>
<!-- //common_code_modal_zone -->

<!-- menu_modal_zone -->
<div id="menu_modal_zone"></div>
<!-- //menu_modal_zone -->

<script>
    //신청자 모달
    function openCommonCodeModal(code_seq) {
        let postData = {
            "code_seq" : code_seq,
        }

        $.ajax({
            url: "../modal/common_code.php",
            type: "POST",
            data: postData,
            success: function (data) {
                //성공
                $("#common_code_modal_zone").html(data);
                $("#common_code_modal").modal("show");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }

    //메뉴 모달
    function openMenuModal(type, page_type, seq, parent_seq) {
        var postData = {
            "type" : type,
            "seq" : seq,
            "parent_seq" : parent_seq,
            "page_type" : page_type,
        }

        $.ajax({
            url: "../modal/menuModal.php",
            type: "POST",
            data: postData,
            success: function (data) {
                //성공
                $("#menu_modal_zone").html(data);
                $("#menu_modal").modal("show");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }

    //메모 모달
    function openMemoModal(seq) {
        var postData = {
            "seq" : seq
        }

        $.ajax({
            url: "../modal/memoModal.php",
            type: "POST",
            data: postData,
            success: function (data) {
                //성공
                $("#menu_modal_zone").html(data);
                $("#menu_modal").modal("show");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    }
</script>