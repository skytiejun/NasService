<?php

// 메타태그를 이용한 URL 이동
function goto_url($url)
{
    echo "<script type='text/javascript'> location.replace('$url'); </script>";
    exit;
}

?>