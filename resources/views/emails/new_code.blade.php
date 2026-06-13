<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $app_name }} - 验证码</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif, 'PingFang SC', 'Microsoft YaHei';
            background-color: #f5f5f5;
            padding: 20px;
        }

        .email-container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 20px;
            text-align: center;
        }

        .email-header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .email-header .subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
        }

        .email-body {
            padding: 30px 25px;
        }

        .email-body .greeting {
            font-size: 16px;
            color: #333333;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .email-body .greeting strong {
            color: #667eea;
        }

        .code-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin-bottom: 20px;
        }

        .code-box .code-label {
            font-size: 14px;
            color: #666666;
            margin-bottom: 12px;
        }

        .code-box .code {
            font-size: 42px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #667eea;
            text-shadow: 0 2px 4px rgba(102, 126, 234, 0.2);
        }

        .email-body .info {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 20px;
        }

        .email-body .info p {
            font-size: 14px;
            color: #856404;
            line-height: 1.6;
            margin: 0;
        }

        .email-body .info strong {
            color: #856404;
        }

        .email-body .warning {
            font-size: 13px;
            color: #888888;
            line-height: 1.6;
            text-align: center;
        }

        .email-body .warning a {
            color: #667eea;
            text-decoration: none;
        }

        .email-body .warning a:hover {
            text-decoration: underline;
        }

        .email-footer {
            background-color: #f8f9fa;
            padding: 20px 25px;
            border-top: 1px solid #e9ecef;
        }

        .email-footer .company {
            font-size: 14px;
            color: #666666;
            text-align: center;
        }

        .email-footer .company strong {
            color: #333333;
        }

        .email-footer .unsubscribe {
            font-size: 12px;
            color: #999999;
            text-align: center;
            margin-top: 10px;
        }

        .category-badge {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>{{ $app_name }}</h1>
            <div class="subtitle">安全验证码</div>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Category Badge -->
            <div class="category-badge">
                {{ $category_text }}
            </div>

            <!-- Greeting -->
            <p class="greeting">
                您好！您收到这封邮件是因为我们收到了您的{{ $app_name }}账户的<strong>{{ $category_text }}</strong>请求。
            </p>

            <!-- Code Box -->
            <div class="code-box">
                <div class="code-label">您的验证码</div>
                <div class="code">{{ $code }}</div>
            </div>

            <!-- Info Box -->
            <div class="info">
                <p>
                    <strong>重要提示：</strong>此验证码将在<strong>{{ $time }}分钟</strong>后过期。
                    请在应用中输入此验证码以完成操作。
                </p>
            </div>

            <!-- Warning -->
            <p class="warning">
                如果您没有发起此请求，请忽略此邮件。除非您输入上述验证码，否则您的密码不会更改。<br>
                请确保您在官方<a href="{{ $url }}">{{ $app_name }}</a>网站上输入敏感信息。
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="company">
                &copy; {{ date('Y') }} <strong>{{ $app_name }}</strong>. 保留所有权利。
            </div>
            <div class="unsubscribe">
                这是自动发送的邮件，请不要回复此邮件。
            </div>
        </div>
    </div>
</body>

</html>