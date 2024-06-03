 

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        body{
            display: flex;
            justify-content: center;
        }
        .main {
            border: none;
            border-radius: 5px;
            box-shadow: 0px 0 30px rgba(1, 41, 112, 0.1);
            width: 30rem;
        }

        .main1 {
            padding: 50px;
        }

        .main1 h1, .main h2, .main1 h3 ,.main1 p{
            
            font-family: "Nunito", sans-serif;
            color: #899bbd;
            font-weight: 600;
        }

        .main1 h3 ,.main1 p{
            font-size: 13px;
        }

        .info{
            line-height: 5px;
        }

        .content .par p{
            line-height: 20px;
        }

        .content .title h1{
            font-size: 24px;
    margin-bottom: 0;
    font-weight: 600;
    color: #012970;
        }
        .main1 button{
            margin-top: 30px;
            color: #fff;
            background-color: #0d6efd;
            border-color: #0d6efd;
            border-radius:  0.375rem;
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            cursor: pointer;
            /* cursor: wait; */
            padding: 0.375rem 0.75rem;
            line-height: 1.5;
            font-weight: 400;
        }

        



        .main1 button:hover{
            color:#fff;
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
    </style>
</head>

<body>
    <div class="main">
        <div class="main1" align="center">
            <div class="img">
                <img src="assets/img/logo1.png" width="100" height="50" alt="">
            </div>

            <div class="content">
                <div class="title" align="center">
                    <h1>Hi $_POST['firstName'] $_POST['lastName']</h1>
                </div>
                <div class="par">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                        labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
                        laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in
                        voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat
                        non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                </div>
            </div>

            <div style="width:100%;height:5px;display:block" align="center">
                <div style="width:100%;max-width:600px;height:1px;border-top:1px solid #e0e0e0">
                </div>
            </div>

            <div class="info">
                <div class="user">
                    <h3>Username</h3>
                    <h2>$_POST['username']</h2>
                </div>
                <div class="pass">
                    <h3>Password</h3>
                    <h2>$_POST['password']</h2>
                </div>
            </div>

            <div class="butt">
                <button>Login to your account</button>
            </div>
        </div>
    </div>
</body>

</html>
