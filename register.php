<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    
    // 입력값 검증
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "아이디를 입력해주세요.";
    }
    
    if (empty($password)) {
        $errors[] = "비밀번호를 입력해주세요.";
    }
    
    if (empty($email)) {
        $errors[] = "이메일을 입력해주세요.";
    }
    
    // 아이디 중복 체크
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "이미 사용 중인 아이디입니다.";
    }
    
    // 이메일 중복 체크
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "이미 사용 중인 이메일입니다.";
    }
    
    if (empty($errors)) {
        try {
            // 비밀번호 해시화
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // 사용자 정보 저장
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$username, $hashed_password, $email]);
            
            // 회원가입 성공 시 로그인 페이지로 리다이렉트
            header("Location: login.html?register=success");
            exit();
        } catch(PDOException $e) {
            $errors[] = "회원가입 중 오류가 발생했습니다.";
        }
    }
    
    // 에러가 있는 경우
    if (!empty($errors)) {
        echo "<script>
            alert('" . implode("\\n", $errors) . "');
            window.location.href = 'register.html';
        </script>";
    }
}
?> 