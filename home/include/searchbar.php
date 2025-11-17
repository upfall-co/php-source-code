<?php
    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    $search_text = get_request_param('search_text', 'GET');
?>

<div id="searchBar" class="header__search_container">
    <div class="searchBox header__search_wrap">
        <input type="text" id="search_text" name="search_text" placeholder="search" value="<?=$search_text?>">
        <button id="search_btn" name="search_btn">
            <img src="<?= homeFoldName ?>/img/search__btn.svg" alt="">
        </button>
    </div>
</div>
<script>
    $('#search_text').on('keydown', function(event) {
        if (event.keyCode === 13) { // 엔터 클릭시
            event.preventDefault();
            $('#search_btn').click(); // 버튼 기능으로 활성화
        }
    });

    $(document).ready(function() {
        $('#search_btn').on('click', function() {
            var query = $('#search_text').val(); // 검색어
            var path = window.location.pathname; // 추후 필요시 활성화 현 파일의 경로를 가져옴
            var searchParams = new URLSearchParams(window.location.search); // 파라미터 값
            var Params = searchParams.has('search_text'); // 파라미터중에서 search_text의 값 확인

            if (!gfn_isNull(Params)) {
                searchParams.set('search_text', query); // 파라미터 search_text의 값이 있으면 값 변경
            } else {
                searchParams.append('search_text', query); // 파라미터 search_text의 값이 없으면 파라미터에 추가
            }

            var newSearch = searchParams.toString(); // 파라미터값을 문자열로 변경해줌

            ufn_change_location(newSearch); // 사용하는 화면에서 값을 지정 ex) notice.php // window.location.href = '/sub05/notice.php?'+ newSearch;
        });
    });
</script>