<!DOCTYPE html>

<head>
    <style>
        /*! normalize.css v8.0.0 | MIT License | github.com/necolas/normalize.css */
        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%
        }

        body {
            margin: 0
        }

        html {
            /*  box-sizing: border-box;*/
            font-family: sans-serif
        }


        .bg-white {
            background-color: #fff
        }
    </style>
</head>
<html lang="en">


<body class="bg-white">
    <p>Xin chào,</p>

    <p>Yêu cầu cấp lại mật khẩu của bạn trên phần mềm quản lý doanh nghiệp thẩm định giá nova.fastvalue.vn đã được thực hiện.</p>

    <p>Tên tài khoản: {{ $email }}</p>

    <p>Mật khẩu mới: {{ $new_password }}</p>

    <p>Đường dẫn truy cập: https://nova.fastvalue.vn</p>

    <p>Trân trọng.</p>

    <p>Tin nhắn tự động, vui lòng không trả lời.</p>

</body>

</html>