<?php
//head
define("SUB", "");
include_once __DIR__ .'/../common/head.php';

try {
    //아이디 저장 여부
    $id = isset($_COOKIE['save_id']) ? $_COOKIE['save_id'] : '';

} catch (Exception $e) {

}

?>
<body class="gray-bg">

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>
                <h1 class="logo-name"><img src="/img/KakaoTalk_20231130_123913398.png?v=<?=IMGYYYYMMDD;?>" alt="logo" title="logo" style="width: 100%; height: 100%;"/></h1>
            </div>
            
            <form class="m-t" role="form" id="frm" method="post" action="../program/login.php">
                <div class="form-group">
                    <input type="text" class="form-control" id="id" name="id" value="<?=$id;?>" placeholder="아이디를 입력해 주세요" required="required">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="pw" name="pw" placeholder="비밀번호를 입력해 주세요" required="required"/>
                </div>

                <div class="form-group row">
                    <div class="col-lg-offset-2 col-lg-10">
                        <div class="i-checks float-left">
                            <label> 
                                <input type="checkbox" id="save_id" name="save_id" value="Y"> 
                                아이디 저장 
                            </label>
                        </div>
                    </div>
                </div>


                <button type="button" class="btn btn-primary block full-width m-b" id="btnLogin" onclick="javascript:check_login();">Login</button>
            </form>
            <p class="m-t"> <small>Inspinia we app framework base on Bootstrap 3 &copy; 2014</small> </p>
        </div>
    </div>
</body>

<script>
    $(function () {
        //focus
        if ($("#id").val() == "") {
            $("#id").focus();
        } else {
            $("#pw").focus();
        }

        //비밀번호란 엔터
        $("#pw").on("keyup", function(e) {
            if (e.keyCode == 13) {
                $("#btnLogin").trigger("click");
            }
        });
    });

    //로그인 체크
    function check_login() {
        //유효성
        //아이디
        if ($.trim($("#id").val()) == "") {
            alert("아이디를 입력해 주세요.");
            $("#id").focus();
            return false;
        }

        //비밀번호
        if ($.trim($("#pw").val()) == "") {
            alert("비밀번호를 입력해 주세요.");
            $("#pw").focus();
            return false;
        }

        if (confirm("로그인 하시겠습니까?")) {
            //submit
            $("#frm").submit();
        }
    }
</script>

</html>
