<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance - {{ global_config('site_name', 'BuatSerba') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .maintenance-container {
            background: #f5f5f5cc;
            border-radius: 20px;
            padding: 50px 20px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(156, 147, 147, 0.3);
        }

        .icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            background: #da000bff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon svg {
            width: 50px;
            height: 50px;
            fill: white;
        }

        h1 {
            color: #17181aff;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        p {
            color: #35373aff;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 30px;
        }

        .brand {
            color: #da000bff;
            font-weight: 600;
        }

        @media (max-width: 640px) {
            .maintenance-container {
                padding: 40px 30px;
            }

            h1 {
                font-size: 24px;
            }

            p {
                font-size: 14px;
            }

            .icon {
                width: 60px;
                height: 60px;
            }

            .icon svg {
                width: 30px;
                height: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                <path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/>
            </svg>
        </div>
        <h1>Under Maintenance</h1>
        <p>
            Saat ini website dalam pemeliharaan sistem.<br>
            Silahkan kembali lagi nanti. Terima kasih.
        </p>
    </div>
</body>
</html>
