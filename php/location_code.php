<?php
/**
 * 파일명 : location_code.php
 * 내용 : location 페이지 코드
 * 최초작성날짜 : 2025/04/07
 * 최초작성자 : 최호준
 * ------------------------------------
 * name       date        comment
 * 최호준    2025/04/07    V1.0
 */

    require_once($_SERVER['DOCUMENT_ROOT']. '/vendor/autoload.php');
    require_once($_SERVER['DOCUMENT_ROOT']. '/lib/m_lib.php');

    use Clef\Pdo7 as Pdo7;
    use Clef\ClefResult as ClefResult;

    /**
     * name :getList_INQUIRY
     * comment : 문의 리스트
     */
    function getList_LOCATION() {

        $arrRtn = array(
              'code' => 500
            , 'msg' => ''
        );

        $mysqldb = new Pdo7();
        $clefResult = new ClefResult();

        try {
            $table = 'LOCATION';

            $sql = "
                 SELECT M_ADDRESS
                      , M_NAVER_LINK
                      , M_KAKAO_LINK
                      , D_ADDRESS
                      , D_NAVER_LINK
                      , D_KAKAO_LINK
                      , OPERATE
                      , PARKKING
                      , FACILITIES
                      , ATTACH_FILE_ID
                   FROM {$table}
                  ORDER BY reg_date DESC";

            $name_sql = 'LOCATION 데이터';
            $clefResult = $mysqldb->select($sql, '', $name_sql);

            if (!$clefResult->getResult()) {
                gfn_isValidation(800);
            }

            $list = $clefResult->getResultSet();

            if (!empty($list)) {
                foreach ($list as $data) {
                    $_db_M_ADDRESS = _check_var($data['M_ADDRESS']); // 제1 주소
                    $_db_M_NAVER_LINK = _check_var($data['M_NAVER_LINK']); // 제1 네이버 맵 링크
                    $_db_M_KAKAO_LINK = _check_var($data['M_KAKAO_LINK']); // 제1 카카오 맵 링크
                    $_db_D_ADDRESS = _check_var($data['M_ADDRESS']); // 제2 주소
                    $_db_D_NAVER_LINK = _check_var($data['M_NAVER_LINK']); // 제2 네이버 맵 링크
                    $_db_D_KAKAO_LINK = _check_var($data['M_KAKAO_LINK']); // 제2 카카오 맵 링크
                    $_db_OPERATE = _check_var($data['OPERATE']); // 운영
                    $_db_PARKKING = _check_var($data['PARKKING']); // 주차
                    $_db_FACILITIES = _check_var($data['FACILITIES']); // 시설
                    $_db_ATTACH_FILE_ID = _check_var($data['ATTACH_FILE_ID']); // 파일아이디
                    $file_html = "";

                    if (!empty($_db_ATTACH_FILE_ID)) {
                        $file_list = gfn_file_upload("S", '', $_db_ATTACH_FILE_ID, 1);

                        if (!empty($file_list)) { // 썸네일
                            foreach ($file_list as $f_list) {
                                $_db_attach_file_temp_name = _check_var($f_list['ATTACH_FILE_TEMP_NAME']); // 파일가상이름
                                $_db_attach_file_path = _check_var($f_list['ATTACH_FILE_PATH']); // 경로
                                $file_html = $_db_attach_file_path. '/'.$_db_attach_file_temp_name;
                            }
                        }
                    }

                    echo <<<A
                                <div class="loca__s1_map" id="loca__s1_map"><img src="{$file_html}" alt="사진"></div>
                                <ul class="loca__s1_info_container">
                                    <li>
                                        <p class="name">주소</p>
                                        <div class="contents_wrap">
                                        <div class="contents">
                                            {$_db_M_ADDRESS}
                                            <a href="{$_db_M_NAVER_LINK}" target="blank">naver</a>
                                            <a href="{$_db_M_KAKAO_LINK}" target="blank">kakao</a>
                                        </div>

                                        <div class="contents">
                                            {$_db_D_ADDRESS}
                                            <a href="{$_db_D_NAVER_LINK}" target="blank">naver</a>
                                            <a href="{$_db_D_KAKAO_LINK}" target="blank">kakao</a>
                                        </div>
                                        </div>
                                    </li>
                                    <li>
                                        <p class="name">운영</p>
                                        <div class="contents_wrap">
                                        <div class="contents">
                                            {$_db_OPERATE}
                                        </div>
                                        </div>
                                    </li>
                                    <li>
                                        <p class="name">주차</p>
                                        <div class="contents_wrap">
                                        <div class="contents">
                                            {$_db_PARKKING}
                                        </div>
                                        </div>
                                    </li>
                                    <li>
                                        <p class="name">시설</p>
                                        <div class="contents_wrap">
                                        <div class="contents">
                                            {$_db_FACILITIES}
                                        </div>
                                        </div>
                                    </li>
                                </ul>
                            A;
                }
            }

        } catch (Exception $e) {
            $arrRtn['code'] = $e -> getCode();
            $arrRtn['msg'] = $e -> getMessage();

            echo json_encode($arrRtn, JSON_UNESCAPED_UNICODE);
        }
    }
?>