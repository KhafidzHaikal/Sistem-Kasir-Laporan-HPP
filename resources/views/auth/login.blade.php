<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login </title>
    <!-- Favicon icon -->
    <link href="/css/style.css" rel="stylesheet">
    <style>
        h3 {
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin-top: 2rem; 
        }
        label {
            display: flex;
            justify-content: center;
        }

        input {
            text-align: center;
        }
        @media (max-width: 768px) {
            img{
                display: none;
            }
            h3{
                text-align: center;
                margin-left: 50px;
            }
            h4{
                text-align: center;
                margin-left: 50px;
            }
        }
    </style>
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container-fluid h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    <form action={{ route('login') }} method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label><strong>Email</strong></label>
                                            <input type="text" class="form-control" name="email">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Password</strong></label>
                                            <input type="password" class="form-control" name="password">
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="/vendor/global/global.min.js"></script>
    <script src="/js/quixnav-init.js"></script>
    <script src="/js/custom.min.js"></script>

</body>

</html>
