<?php
/*
if(!define('DIR_APP'))
	die ('Your have not permission');
*/
class SBD_Common{
	/*

     * Tên hàm: Url Rewrite

     * Mô tả: Tạo địa chỉ URL thân thiện

     * Tham số đầu vào: [string] đường dẫn URL ($str), 

     * 			[string] tiêu đề bài viết,... ($title)

     * Tham số đầu ra: [string] đường dẫn thân thiện hơn

     * Ví dụ:

     * $com = new Common;

     * $com->url_rewrite("index.php?p=news&q=detail&id=1");

     * => news/detail/1

     * 

     */



    public function url_rewrite($str, $title = '') {
        global $mod_rewrite;
        $arr = array('/p=/', '/q=/', '/id=/', '/page=/');
        $dir = DIR_APP == 'admin' ? 'admin/' : '';
        $title = empty($title) ? '' : '/' . $this->label_rewrite(trim($title)) . ".html";
        $url = explode('?', $str);
        $url = str_replace('&', '/', @$url[1]);
        $url = preg_replace($arr, '', $url) . $title;
        return $mod_rewrite == true ? BASE_NAME . $dir . $url : $str;
    }



    /*

     * Tên hàm: Rand String

     * Mô tả: Tạo ngẫu nhiên 1 chuỗi với độ dài tùy chỉnh

     * Tham số đầu vào: [int] chiều dài ký tự muốn lấy ($length)

     * Tham số đầu ra: [string] một chuỗi ký tự với độ dài đã tùy chỉnh

     * Ví dụ:

     * $com = new Common;

     * $com->rand_string(4);

     * 

     */



    public function rand_string($length) {

        $chars = "abcdefghijklmnopqrstuvwxyz%#&_-ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        $size = strlen($chars);

        $str = "";

        for ($i = 0; $i < $length; $i++) {

            $str .= $chars[rand(0, $size - 1)];

        }

        return $str;

    }



    /*

     * Tên hàm: Label Rewrite

     * Mô tả: chuyển đổi định dạng tiêu đề bài viết từ UTF-8 thành ANSI

     * Tham số đầu vào: [string] tiêu đề bài viết ($label)

     * Tham số đầu ra: [string] tiêu đề bài viết sau khi định dạng

     * Ví dụ:

     * $com = new Common;

     * $com->label_rewrite("Thành phố Hồ Chi Minh");

     * => thanh-pho-ho-chi-minh

     * 

     */



    public function label_rewrite($text) {

        $text = str_replace(

                array(' ', '%', "/", "\\", '"', '?', '<', '>', "#", "^", "`", "'", "=", "!", ":", ",,", "..", "*", "&", "__", "▄", "-"), array('-', '', '', '', '', '', '', '', '', '', '', '', '-', '', '-', '', '', '', "va", "-", "-", "-"), $text);



        $chars = array("a", "A", "e", "E", "o", "O", "u", "U", "i", "I", "d", "D", "y", "Y");



        $uni[0] = array("á", "à", "ạ", "ả", "ã", "â", "ấ", "ầ", "ậ", "ẩ", "ẫ", "ă", "ắ", "ằ", "ặ", "ẳ", "� �");

        $uni[1] = array("Á", "À", "Ạ", "Ả", "Ã", "Â", "Ấ", "Ầ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ắ", "Ằ", "Ặ", "Ẳ", "� �");

        $uni[2] = array("é", "è", "ẹ", "ẻ", "ẽ", "ê", "ế", "ề", "ệ", "ể", "ễ");

        $uni[3] = array("É", "È", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ế", "Ề", "Ệ", "Ể", "Ễ");

        $uni[4] = array("ó", "ò", "ọ", "ỏ", "õ", "ô", "ố", "ồ", "ộ", "ổ", "ỗ", "ơ", "ớ", "ờ", "ợ", "ở", "� �");

        $uni[5] = array("Ó", "Ò", "Ọ", "Ỏ", "Õ", "Ô", "Ố", "Ồ", "Ộ", "Ổ", "Ỗ", "Ơ", "Ớ", "Ờ", "Ợ", "Ở", "� �");

        $uni[6] = array("ú", "ù", "ụ", "ủ", "ũ", "ư", "ứ", "ừ", "ự", "ử", "ữ");

        $uni[7] = array("Ú", "Ù", "Ụ", "Ủ", "Ũ", "Ư", "Ứ", "Ừ", "Ự", "Ử", "Ữ");

        $uni[8] = array("í", "ì", "ị", "ỉ", "ĩ");

        $uni[9] = array("Í", "Ì", "Ị", "Ỉ", "Ĩ");

        $uni[10] = array("đ");

        $uni[11] = array("Đ");

        $uni[12] = array("ý", "ỳ", "ỵ", "ỷ", "ỹ");

        $uni[13] = array("Ý", "Ỳ", "Ỵ", "Ỷ", "Ỹ");



        for ($i = 0; $i <= 13; $i++) {

            $text = str_replace($uni[$i], $chars[$i], $text);

        }

        return strtolower($text);

    }

	public function label_rewrite_uni($text) {

        $text = str_replace(

                array(' ', '%', "/", "\\", '"', '?', '<', '>', "#", "^", "`", "'", "=", "!", ":", ",,", "..", "*", "&", "__", "▄", "-"), array(' ', '', '', '', '', '', '', '', '', '', '', '', '-', '', '-', '', '', '', "va", "-", "-", "-"), $text);



        $chars = array("a", "A", "e", "E", "o", "O", "u", "U", "i", "I", "d", "D", "y", "Y");



        $uni[0] = array("á", "à", "ạ", "ả", "ã", "â", "ấ", "ầ", "ậ", "ẩ", "ẫ", "ă", "ắ", "ằ", "ặ", "ẳ", "� �");

        $uni[1] = array("Á", "À", "Ạ", "Ả", "Ã", "Â", "Ấ", "Ầ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ắ", "Ằ", "Ặ", "Ẳ", "� �");

        $uni[2] = array("é", "è", "ẹ", "ẻ", "ẽ", "ê", "ế", "ề", "ệ", "ể", "ễ");

        $uni[3] = array("É", "È", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ế", "Ề", "Ệ", "Ể", "Ễ");

        $uni[4] = array("ó", "ò", "ọ", "ỏ", "õ", "ô", "ố", "ồ", "ộ", "ổ", "ỗ", "ơ", "ớ", "ờ", "ợ", "ở", "� �");

        $uni[5] = array("Ó", "Ò", "Ọ", "Ỏ", "Õ", "Ô", "Ố", "Ồ", "Ộ", "Ổ", "Ỗ", "Ơ", "Ớ", "Ờ", "Ợ", "Ở", "� �");

        $uni[6] = array("ú", "ù", "ụ", "ủ", "ũ", "ư", "ứ", "ừ", "ự", "ử", "ữ");

        $uni[7] = array("Ú", "Ù", "Ụ", "Ủ", "Ũ", "Ư", "Ứ", "Ừ", "Ự", "Ử", "Ữ");

        $uni[8] = array("í", "ì", "ị", "ỉ", "ĩ");

        $uni[9] = array("Í", "Ì", "Ị", "Ỉ", "Ĩ");

        $uni[10] = array("đ");

        $uni[11] = array("Đ");

        $uni[12] = array("ý", "ỳ", "ỵ", "ỷ", "ỹ");

        $uni[13] = array("Ý", "Ỳ", "Ỵ", "Ỷ", "Ỹ");



        for ($i = 0; $i <= 13; $i++) {

            $text = str_replace($uni[$i], $chars[$i], $text);

        }

        return strtolower($text);

    }

    /*

     * Tên hàm: Text Rewrite

     * Mô tả: chức năng giống hàm "label_rewrite" nhưng để chuyển đổi 1 đoạn văn

     * Tham số đầu vào: [string] tương tự hàm "label_rewrite" ($text)

     * Tham số đầu ra: [string] tương tự hàm "label_rewrite"

     * Ví dụ:

     * $com = new Common;

     * $com->text_rewrite(< đoạn văn >);

     * 

     */



    public function text_rewrite($text) {
		
        $text = str_replace(

                array(' ', '%', "/", "\\", '"', '?', '<', '>', "#", "^", "`", "'", "=", "!", ":", ",,", "..", "*", "&", "__", "▄", "-"), array('-', '', '', '', '', '', '', '', '', '', '', '', '-', '', '-', '', '', '', "va", "-", "-", "-"), $text);
		
        $chars = array("a", "A", "e", "E", "o", "O", "u", "U", "i", "I", "d", "D", "y", "Y");



        $uni[0] = array("á", "à", "ạ", "ả", "ã", "â", "ấ", "ầ", "ậ", "ẩ", "ẫ", "ă", "ắ", "ằ", "ặ", "ẳ", "� �");

        $uni[1] = array("Á", "À", "Ạ", "Ả", "Ã", "Â", "Ấ", "Ầ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ắ", "Ằ", "Ặ", "Ẳ", "� �");

        $uni[2] = array("é", "è", "ẹ", "ẻ", "ẽ", "ê", "ế", "ề", "ệ", "ể", "ễ");

        $uni[3] = array("É", "È", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ế", "Ề", "Ệ", "Ể", "Ễ");

        $uni[4] = array("ó", "ò", "ọ", "ỏ", "õ", "ô", "ố", "ồ", "ộ", "ổ", "ỗ", "ơ", "ớ", "ờ", "ợ", "ở", "� �");

        $uni[5] = array("Ó", "Ò", "Ọ", "Ỏ", "Õ", "Ô", "Ố", "Ồ", "Ộ", "Ổ", "Ỗ", "Ơ", "Ớ", "Ờ", "Ợ", "Ở", "� �");

        $uni[6] = array("ú", "ù", "ụ", "ủ", "ũ", "ư", "ứ", "ừ", "ự", "ử", "ữ");

        $uni[7] = array("Ú", "Ù", "Ụ", "Ủ", "Ũ", "Ư", "Ứ", "Ừ", "Ự", "Ử", "Ữ");

        $uni[8] = array("í", "ì", "ị", "ỉ", "ĩ");

        $uni[9] = array("Í", "Ì", "Ị", "Ỉ", "Ĩ");

        $uni[10] = array("đ");

        $uni[11] = array("Đ");

        $uni[12] = array("ý", "ỳ", "ỵ", "ỷ", "ỹ");

        $uni[13] = array("Ý", "Ỳ", "Ỵ", "Ỷ", "Ỹ");



        for ($i = 0; $i <= 13; $i++) {

            $text = str_replace($uni[$i], $chars[$i], $text);

        }

        return strtolower($text);

    }



    /*

     * Tên hàm: Encrypt

     * Mô tả: 

     * Tham số đầu vào: 

     * Tham số đầu ra: 

     * Ví dụ:

     * $com = new Common;

     * $com->encrypt();

     * 

     */



    public function encrypt($x, $key) {

        $x .=$key;

        $s = '';

        foreach (str_split($x) as $c)

            $s.=sprintf("%02X", ord($c));

        return($s);

    }



    /*

     * Tên hàm: Decrypt

     * Mô tả: 

     * Tham số đầu vào: 

     * Tham số đầu ra: 

     * Ví dụ:

     * $com = new Common;

     * $com->decrypt();

     * 

     */



    public function decrypt($x, $key) {

        $x .=$key;

        $s = '';

        foreach (explode("\n", trim(chunk_split($x, 2))) as $h)

            $s.=chr(hexdec($h));



        return(substr($s, 0, -3));

    }



    /*

     * Tên hàm: Format Number

     * Mô tả: Tạo ngẫu nhiên 1 chuỗi với độ dài tùy chỉnh

     * Tham số đầu vào: [int] chiều dài ký tự muốn lấy

     * Tham số đầu ra: [string] một chuỗi ký tự với độ dài đã tùy chỉnh

     * Ví dụ:

     * $com = new Common;

     * $com->rand_string(4);

     * 

     */



    public function format_number($number, $ext = "VNĐ") {

        $strResult = "";



        if ($ext == "")

            $strResult = number_format($number, 0, ',', '.');

        else

            $strResult = number_format($number, 0, ',', '.') . " VNĐ";



        return $strResult;

    }



    /*

     * Tên hàm: Rand String

     * Mô tả: Tạo ngẫu nhiên 1 chuỗi với độ dài tùy chỉnh

     * Tham số đầu vào: [int] chiều dài ký tự muốn lấy

     * Tham số đầu ra: [string] một chuỗi ký tự với độ dài đã tùy chỉnh

     * Ví dụ:

     * $com = new Common;

     * $com->rand_string(4);

     * 

     */



    public function formatCurreny($number, $float) {

        return number_format($number, $float, '.', '');

    }



    /*

     * Tên hàm: Get Rate_USD

     * Mô tả: Lấy tỉ giá USD

     * Tham số đầu vào:

     * Tham số đầu ra:

     * Ví dụ:

     * $com = new Common;

     * $com->get_rate_USD();

     * 

     */



    public function get_rate_USD() {

        $data = file_get_contents("http://www.vietcombank.com.vn/exchangerates/ExrateXML.aspx");

        $p = explode("<Exrate ", $data);

        $tg = array();

        for ($a = 1; $a < count($p); $a++) {

            if (strpos($p[$a], 'USD')) {

                $posCurrencyCode = strrpos($p[$a], 'CurrencyCode="') + 14;

                $CurrencyCode = substr($p[$a], $posCurrencyCode, 3);



                $posBuy = strrpos($p[$a], 'Buy="') + 5;

                $priceBuy = floatval(substr($p[$a], $posBuy, 8));

                $posTransfer = strrpos($p[$a], 'Transfer="') + 10;

                $priceTransfer = floatval(substr($p[$a], $posTransfer, 6));

                $posSell = strrpos($p[$a], 'Sell="') + 6;

                $priceSell = floatval(substr($p[$a], $posSell, 8));

                $tg[$a] = array('CurrencyCode' => $CurrencyCode, 'Buy' => $priceBuy, 'Transfer' => $priceTransfer, 'Sell' => $priceSell);

            }

        }

        return $tg[18]['Buy'];

    }



    /*

     * Tên hàm: Alert Redirect

     * Mô tả: Xuất ra thông báo và chuyển đến trang tùy chỉnh

     * Tham số đầu vào: [string] thông báo ($msg),

     *                  [string] tiêu đề dialog ($title_msg),

     *                  [string] đường dẫn muốn chuyển đến ($strUrl)

     * Tham số đầu ra:

     * Ví dụ:

     * $com = new Common;

     * $com->aRedirect("Chào bạn", "TMV-CMS thông báo", "index.php");

     * 

     */



    public function alert_redirect($msg, $title_msg, $strUrl) {

        ?>

        <script>

            alert('<?php echo $msg ?>');

            window.location.href='<?php echo $strUrl ?>';

        </script>

        <?php

    }



    /*

     * Tên hàm: Redirect

     * Mô tả: Chuyển đến trang tùy chỉnh

     * Tham số đầu vào: [string] đường dẫn muốn chuyển đến ($str)

     *                  [string] tiêu đề bài viết,... ($title)

     * Tham số đầu ra:

     * Ví dụ:

     * $com = new Common;

     * $com->redirect("index.php");

     * 

     */



    public function redirect($str = '', $title = '') {

        echo "<script>window.location.href='" . $this->url_rewrite($str, $title) . "'</script>";

        exit();

    }



    /*

     * Tên hàm: Get Real IPAddress

     * Mô tả: lấy địa chỉ IP của Client

     * Tham số đầu vào: 

     * Tham số đầu ra: [string] địa chỉ IP của Client

     * Ví dụ:

     * $com = new Common;

     * $com->get_real_IPAddress();

     * 

     */



    public function get_real_IPAddress() {

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {

            $ip = $_SERVER['HTTP_CLIENT_IP'];

        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

        } else {

            $ip = $_SERVER['REMOTE_ADDR'];

        }

        return $ip;

    }



    /*

     * Tên hàm: Cut String

     * Mô tả: cắt chuỗi với độ dài tùy chỉnh

     * Tham số đầu vào: [string] chuỗi muốn cắt ($strorg)

     *                  [int] độ dài muốn lấy ($limit)

     * Tham số đầu ra: [string] chuỗi sau khi cắt

     * Ví dụ:

     * $com = new Common;

     * $com->cut_string(< chuỗi muốn cắt >, 30 ); // lấy 30 ký tự đầu của chuỗi truyền vào

     * 

     */



    public function cut_string($strorg, $limit) {

        if (strlen($strorg) <= $limit) {

            return $strorg;

        } else {

            if (strpos($strorg, " ", $limit) > $limit) {

                $new_limit = strpos($strorg, " ", $limit);

                $new_strorg = substr($strorg, 0, $new_limit) . "...";

                return $new_strorg;

            }

            $new_strorg = substr($strorg, 0, $limit) . "...";

            return $new_strorg;

        }

    }



    /*

     * Tên hàm: Send Mail SMTP

     * Mô tả: gửi mail

     * Tham số đầu vào: [string] email muốn gửi đến ($to)

     *                  [string] email gửi đi ($from)

     *                  [string] tên người gủi ($from_name)

     *                  [string] tiêu đề mail ($subject)

     *                  [string] nội dung mail ($message)

     *                  [string] chữ ký ($signature)

     *                  [string] mail CC ($cc)

     *                  [string] file đính kèm ($attach_file)

     *                  [string] mail BCC ($bc)

     *                  [string] SMTP SERVER ($smtp_server)

     *                  [string] SMTP USER ($smtp_user)

     *                  [string] SMTP PASSWORD ($smtp_pass)

     * Tham số đầu ra: [bool] 1: Thành công, 0: Thất bại

     * Ví dụ:

     * $com = new Common;

     * $com->send_mail_smtp("nhatco.it@gmail.com", "nhatco.it@hotmail.com", "Nhất Cơ", "Tiêu đề mail", "Nội dung mail");

     * 

     */



    public function send_mail_smtp($to, $from, $from_name, $subject, $message, $signature = "", $cc = "", $attach_file = "", $bc = "", $smtp_server = SERVER_SMTP, $smtp_user = USER_SMTP, $smtp_pass = PASSWORD_SMTP) {

        require_once("mailer/class.phpmailer.php");

        require_once("mailer/class.smtp.php");

        $mail = new phpmailer();

        $mail->IsSMTP();                      // send via SMTP

        $mail->Host = $smtp_server; // SMTP servers

        $mail->SMTPAuth = true;     // turn on SMTP authentication

        $mail->Username = $smtp_user;  // SMTP username

        $mail->Password = $smtp_pass; // SMTP password

        $mail->From = $from;

        $mail->FromName = $from_name;

        $mail->AddAddress($to, ""); //send to 

        //cc mail

        if ($cc != "") {

            $arr_cc = explode(',', $cc);

            for ($i = 0; $i < count($arr_cc); $i++) {

                $mail->AddCC($arr_cc[$i]);

            }

        }

        //bc mail

        if ($bc != "") {

            $arr_bc = explode(',', $bc);

            for ($i = 0; $i < count($arr_bc); $i++) {

                $mail->AddBCC($arr_bc[$i]);

            }

        }



        $mail->AddReplyTo($from_name, $from);

        $mail->WordWrap = 50;                              // set word wrap

        $mail->IsHTML(true);                               // send as HTML



        $mail->Subject = $subject;

        if ($signature != "") {

            $message .=$signature;

        }

        $mail->Body = $message;

        $mail->AltBody = "";

        //attach files

        if ($attach_file != "") {

            $arr_attach = explode(',', $attach_file);

            for ($i = 0; $i < count($arr_attach); $i++) {

                $mail->AddAttachment($arr_attach[$i]);

            }

        }

        //do send

        if (!$mail->Send()) {

            $result = 0;

        } else {

            $result = 1;

        }

        return $result;

    }



    /*

     * Tên hàm: Format Date

     * Mô tả: định dạng ngày giờ

     * Tham số đầu vào: [string] ngày giờ muốn định dạng ($date)

     *                  [string] định dạng ($format)

     * Tham số đầu ra: [string] ngày giờ sau khi định dạng

     * Ví dụ:

     * $com = new Common;

     * $com->format_date('2000-01-01', 'Y-m-d H:i:s');

     * 

     */



    public function format_date($datetime) {

		$date = new DateTime($datetime);

		return $date->format('d-m-Y H:i:s');

    }



    /*

     * Tên hàm: Time Difference

     * Mô tả: khoảng thời gian

     * Tham số đầu vào: [string] ngày giờ muốn định dạng ($date)

     *                  [string] định dạng ($format)

     * Tham số đầu ra: [string] ngày giờ sau khi định dạng

     * Ví dụ:

     * $com = new Common;

     * $com->time_difference('2000-01-01 3:19', '2011-01-01 3:19');

     * 

     */

    public function time_difference($start, $end) {

        $uts['start'] = $start;

        $uts['end'] = $end;

        if ($uts['start'] !== -1 && $uts['end'] !== -1) {

            if ($uts['end'] >= $uts['start']) {

                $diff = $uts['end'] - $uts['start'];

                if ($days = intval((floor($diff / 86400))))

                    $diff = $diff % 86400;

                if ($hours = intval((floor($diff / 3600))))

                    $diff = $diff % 3600;

                if ($minutes = intval((floor($diff / 60))))

                    $diff = $diff % 60;

                $diff = intval($diff);

                return( array('day' => $days, 'hour' => $hours, 'min' => $minutes, 'sec' => $diff) );

            }

            else {

                trigger_error("Ending date/time is earlier than the start date/time", E_USER_WARNING);

            }

        } else {

            trigger_error("Invalid date/time data detected", E_USER_WARNING);

        }

        return( false );

    }

    

    /*

     * Tên hàm: ADD CSS

     * Mô tả: thêm CSS 

     * Tham số đầu vào: [array] mảng css

     * Tham số đầu ra: [string] chuỗi css

     * Ví dụ:

     * $com = new Common;

     * $com->addCss('<link href="style.css" rel="stylesheet" type="text/css" />', '<link href="style_2.css" rel="stylesheet" type="text/css" />');

     *

     */

    public function addCss(array $cssData)

    {

    	if(!empty($cssData) > 0)

    	{

    		$css_file = "";

    		foreach($cssData as $k=>$v)

    		{

    			$css_file .= $v."\n\n";

    		}

    		$_SESSION['file_css'] = $css_file;

    		return $_SESSION['file_css'];

    			

    	}

    	else{

    		return FALSE;

    	}

    }

    

    

    

    /*

     * Tên hàm: Time Difference

     * Mô tả: khoảng thời gian

     * Tham số đầu vào: [string] ngày giờ muốn định dạng ($date)

     *                  [string] định dạng ($format)

     * Tham số đầu ra: [string] ngày giờ sau khi định dạng

     * Ví dụ:

     * $com = new Common;

     * $com->time_difference('2000-01-01 3:19', '2011-01-01 3:19');

     *

     */

    public function addJs($jsData)

    {

    	if(count($cssData) > 0)

    	{

    		$js_file = "";

    		foreach($jsData as $k=>$v)

    		{

    			$js_file .=$v."\n\n";

    		}

    		$_SESSION['file_js'] = $js_file;

    		return 	$_SESSION['file_js'];

    	}

    	else

    	{

    		return FALSE;

    	}

    }

	public function upload($file, $rename = '', $dir = 'upload/') {		

		$tmp = $_FILES [$file] ['tmp_name'];

		$name = $_FILES [$file] ['name'];

		if (is_array ( $name )) {

			for($i = 0; $i < count ( $name ); $i ++) {

				$ext [$i] = strtolower ( strrchr ( $name [$i], '.' ) );

				$name [$i] = empty ( $rename ) ? $name [$i] : $rename . $i . $ext [$i];

				if (move_uploaded_file ( $tmp [$i], $dir . $name [$i] )) {

					chmod ( $dir, 0777 );

					$arr [] = $name [$i];

				}

			}

			return $arr;

		} else {

			$ext = strtolower ( strrchr ( $name, '.' ) );

			$name = empty ( $rename ) ? $name : $rename . $ext;

			if (move_uploaded_file ( $tmp, $dir . $name )) {

				chmod ( $dir, 0777 );

				return $name;

			}

		}		

	}

	function uploadUser($file, $rename='', $dir='upload/'){

		if($_SESSION['member'])

		{

			$tmp = $_FILES[$file]['tmp_name'];

			$name = $_FILES[$file]['name'];

			$size = $_FILES[$file]['size'];			

			

			//check if its image file

			if (!getimagesize($tmp))

			{ 

				echo "Invalid Image File...";

				exit();

			}

			$blacklist = array(".php", ".phtml", ".php3", ".php4", ".js", ".shtml", ".pl" ,".py");

			foreach ($blacklist as $file)

			{

				if(preg_match("/$file\$/i", $name))

				{

					echo "ERROR: Uploading executable files Not Allowed\n";

					exit;

				}

			}

				

			if(is_array($name)){

				for($i=0; $i<count($name); $i++){

					$ext[$i] = strtolower(strrchr($name[$i],'.'));

					$name[$i] = empty($rename) ? $name[$i] : $rename . $i . $ext[$i];

					if( move_uploaded_file($tmp[$i], $dir.$name[$i]) ){

						chmod($dir, 0774);

						$arr[] = $name[$i];

					}

				}

				return $arr;

			}

			else{

				$ext = strtolower(strrchr($name,'.'));

				$name = empty($rename) ? $name : $rename . $ext;

				if( move_uploaded_file($tmp, $dir.$name) ){

					chmod($dir, 0774);

					return $name;

				}

			}

		}

	}

	public function createThumbnail($image, $largeur_max, $hauteur_max, $source, $destination, $prefixe) {

		// echo $source.$image;

		if (! file_exists ( $source . $image )) {

			echo "Error: no directory";

			exit ();

		}

		$ext = strtolower ( strrchr ( $image, '.' ) );

		if ($ext == ".jpg" || $ext == ".jpeg" || $ext == ".gif" || $ext == ".png" || $ext == ".JPG" || $ext == ".JPEG") {

			$size = getimagesize ( $source . $image );

			$largeur_src = $size [0];

			$hauteur_src = $size [1];

			if ($size [2] == 1 || $size [2] != 2 || $size [2] != 3 || $size [2] != 6) {

				if ($size [2] == 1) {

					// format gif

					$image_src = imagecreatefromgif ( $source . $image );

				}

				if ($size [2] == 2) {

					// format jpg ou jpeg

					$image_src = imagecreatefromjpeg ( $source . $image );

				}

				if ($size [2] == 3) {

					// format png

					$image_src = imagecreatefrompng ( $source . $image );

				}

				if ($size [2] == 6) {

					// format bmp

					$image_src = imagecreatefromwbmp ( $source . $image );

				}

				if ($largeur_src > $largeur_max or $hauteur_src > $hauteur_max) {

					if ($hauteur_src <= $largeur_src) {

						$ratio = $largeur_max / $largeur_src;

					} else {

						$ratio = $hauteur_max / $hauteur_src;

					}

				} else {

					$ratio = 1;

				}

				if ($largeur_src / $hauteur_src >= 1 && $largeur_src / $hauteur_src <= 1.5) {

					$newHeight = $hauteur_max;

					$newWidth = ($largeur_src / $hauteur_src) * $hauteur_max;

					$image_dest = imagecreatetruecolor ( $newWidth, $newHeight );

					imagecopyresampled ( $image_dest, $image_src, 0, 0, 0, 0, $newWidth, $newHeight, $largeur_src, $hauteur_src );

				} else {

					$image_dest = imagecreatetruecolor ( round ( $largeur_src * $ratio ), round ( $hauteur_src * $ratio ) );

					imagecopyresampled ( $image_dest, $image_src, 0, 0, 0, 0, round ( $largeur_src * $ratio ), round ( $hauteur_src * $ratio ), $largeur_src, $hauteur_src );

				}

				/*

				 * $image_dest=imagecreatetruecolor(round($largeur_src*$ratio),

				 * round($hauteur_src*$ratio));

				 * imagecopyresampled($image_dest,$image_src,0,0,0,0,round($largeur_src*$ratio),round($hauteur_src*$ratio),$largeur_src,$hauteur_src);

				 */

				if (! imagejpeg ( $image_dest, $destination . $prefixe . $image )) {

					exit ();

				}

			}

		}

	}

}

?>