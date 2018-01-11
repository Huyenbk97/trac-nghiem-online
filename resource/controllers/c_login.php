<?php

require_once 'controller.php';
/**
 * Controller Login
 * Author: Dzu
 * Mail: dzu6996@gmail.com
 **/
class C_Login extends Controller
{
    public function showLogin()
    {
        $this->loadView("login");
        $login = new V_Login();
        $login->showLogin();
    }
    public function forGotPassWord()
    {
     $this->loadView("login");
     $login = new V_Login();
     $login->forGotPassWord();
       //goi thu vien
     if(isset($_POST['forgotpw']))
     {
        require_once 'res/libs/class.phpmailer.php';
        require_once 'res/libs/class.smtp.php';
        $email = Htmlspecialchars($_POST['email']);
        $chuc_vu = Htmlspecialchars($_POST['chuc_vu']);
        $get = $this->getPassWord($email,$chuc_vu);
        if($get)
        {
            $passWord = $get->mat_khau;
            $nameTo = $get->ten;
            $mailTo = $email;
            $mail = $this->sendMail($passWord, $nameTo, $mailTo);
            if($mail)
            {
                $status = "Gửi email thành công. Kiểm tra hộp thư để lấy mật khẩu (có thể trong hộp thư rác)";
                echo '<META http-equiv="refresh" content="3;URL=index.php">';
            }
            else
                $status = "Gửi email thất bại, vui lòng thử lại trong giây lát.";
        }
        else
            $status = "Email không tồn tại, vui lòng thử lại";
        $this->loadView("login");
        $login = new V_Login();
        $login->status($status);
    }
}
//hàm kiểm tra email nhập vào có tồn tại không và cập nhật mật khẩu mới
public function getPassWord($email,$chuc_vu)
{
    $this->loadModel('login');
    $login = new M_Login();
    return $login->getPassWord($email,$chuc_vu);
}
public function sendMail($pw, $nTo, $mTo){
    $nFrom = 'IKun.Org';
    $mFrom = 'dzu.ictu@gmail.com';
    $mPass = 'dmdm1122';
    $mail = new PHPMailer();
    $content = 'Đây là mật khẩu của bạn.<br /><b>'.$pw.'</b><br />Hãy đổi ngay sau khi đăng nhập. <br />Đây là email gửi tự động, vui lòng không trả lời email này.';
    $body = $content;
    $mail->IsSMTP(); 
    $mail->CharSet  = "utf-8";
    $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
    $mail->SMTPAuth   = true;                   // enable SMTP authentication
    $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
    $mail->Host       = "smtp.gmail.com";       
    $mail->Port       = 465;
    $mail->Username   = $mFrom;  // GMAIL username
    $mail->Password   = $mPass;              // GMAIL password
    $mail->SetFrom($mFrom, $nFrom);
    $title = 'Reset Mật Khẩu Trên Hệ Thống Trắc Nghiệm Online';
    $mail->Subject    = $title;
    $mail->MsgHTML($body);
    $address = $mTo;
    $mail->AddAddress($address, $nTo);
    $mail->AddReplyTo('noreply24@ikun.org', 'IKun.Org');
    if(!$mail->Send()) {
        return false;
    } else {
        return true;
    }
}
public function checkLogin()
{
    $this->loadView("login");
    $login = new V_Login();
    $status = "Đang kiểm tra......";
    $login->status($status);
    if(isset($_POST['tai_khoan']))
        $tai_khoan = addslashes($_POST['tai_khoan']);
    if(isset($_POST['mat_khau']))
        $mat_khau = md5($_POST['mat_khau']);
    if(isset($_POST['chuc_vu']))
    {
        $chuc_vu = $_POST['chuc_vu'];
            //gọi hàm đăng nhập theo $chuc_vu
        $user = $this->$chuc_vu($tai_khoan,$mat_khau);
        if($user == true)
        {
            $status = "Đăng nhập thành công. Chuẩn bị chuyển trang....";
            $login->status($status);
            echo '<meta http-equiv="refresh" content="1.5" />';
            if($chuc_vu=="giaoVien")
                $_SESSION['id_gv'] = $user->id_gv;
            if($chuc_vu=="admin")
                $_SESSION['id_admin'] = $user->id_admin;
            if($chuc_vu=="hocSinh")
            {
                $_SESSION['id_hs'] = $user->id_hs;
                $_SESSION['id_lop'] = $user->id_lop;
            }
            $_SESSION['tai_khoan'] = $user->tai_khoan;
            $_SESSION['ten'] = $user->ten;
            $_SESSION['chuc_vu'] = $user->chuc_vu;
            $_SESSION['login'] = true;
        }
        else
        {
            $status = "Đăng nhập thất bại, vui lòng kiếm tra lại tài khoản hoặc mật khẩu. Nêu quên mật khẩu, chọn 'Quên Mật Khẩu' để được gíup đỡ.";
            $login->status($status);
        }
    }
}
    // hàm kiểm tra đăng nhập tài khoản ngừoi dùng giáo viên và trả kết quả về $user
public function giaoVien($tai_khoan,$mat_khau)
{
    $this->loadModel("giao_vien");
    $login_gv = new M_Giao_Vien();
    $user = $login_gv->login($tai_khoan,$mat_khau);
    return $user;

}
    // hàm kiểm tra đăng nhập tài khoản ngừoi dùng hoc sinh và trả kết quả về $user
public function hocSinh($tai_khoan,$mat_khau)
{
    $this->loadModel("hoc_sinh");
    $login_hs = new M_Hoc_Sinh();
    $user = $login_hs->login($tai_khoan,$mat_khau);
    return $user;

}
    // hàm kiểm tra đăng nhập tài khoản ngừoi dùng  admin và trả kết quả về $user
public function admin($tai_khoan,$mat_khau)
{
    $this->loadModel("admin");
    $login_hs = new M_Admin();
    $user = $login_hs->login($tai_khoan,$mat_khau);
    return $user;
}
}
?>