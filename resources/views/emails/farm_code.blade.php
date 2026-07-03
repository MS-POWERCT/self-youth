<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>验证码</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace, 'PingFang SC', 'Microsoft YaHei';
            background-color: #0d0d0d;
            padding: 20px;
        }

        .email-container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #1a1a1a;
            border: 1px solid #333;
            border-radius: 4px;
            overflow: hidden;
        }

        .email-header {
            background-color: #222;
            padding: 16px;
            border-bottom: 1px solid #333;
            text-align: center;
        }

        .email-header h1 {
            color: #4ade80;
            font-size: 16px;
            font-weight: normal;
            margin-bottom: 4px;
            letter-spacing: 2px;
        }

        .email-header .subtitle {
            color: #666;
            font-size: 12px;
        }

        .email-body {
            padding: 20px;
            line-height: 1.8;
        }

        .category-badge {
            display: inline-block;
            background-color: #1f2937;
            border: 1px solid #4ade80;
            color: #4ade80;
            padding: 2px 8px;
            font-size: 11px;
            margin-bottom: 12px;
            letter-spacing: 1px;
        }

        .email-body .greeting {
            color: #aaa;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .email-body .greeting strong {
            color: #4ade80;
        }

        .code-box {
            background-color: #0d0d0d;
            border: 1px solid #333;
            padding: 16px;
            text-align: center;
            margin-bottom: 16px;
        }

        .code-box .code-label {
            color: #666;
            font-size: 11px;
            margin-bottom: 8px;
        }

        .code-box .code {
            font-size: 32px;
            font-weight: bold;
            color: #4ade80;
            letter-spacing: 4px;
            font-family: 'Consolas', 'Monaco', monospace;
        }

        .email-body .info {
            background-color: #1c1917;
            border-left: 3px solid #f59e0b;
            padding: 12px;
            margin-bottom: 16px;
        }

        .email-body .info p {
            color: #d6d3d1;
            font-size: 12px;
            margin: 0;
            line-height: 1.6;
        }

        .email-body .info strong {
            color: #f59e0b;
        }

        .email-body .warning {
            color: #666;
            font-size: 11px;
            text-align: center;
            line-height: 1.6;
        }

        .email-footer {
            background-color: #0d0d0d;
            padding: 14px;
            border-top: 1px solid #222;
        }

        .email-footer .company {
            color: #444;
            font-size: 11px;
            text-align: center;
        }

        .email-footer .unsubscribe {
            color: #333;
            font-size: 10px;
            text-align: center;
            margin-top: 6px;
        }

        .divider {
            border-top: 1px dashed #333;
            margin: 16px 0;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>【 安全验证码 】</h1>
            <div class="subtitle">━━━━━━━━━━━━━━━━━</div>
        </div>

        <div class="email-body">
            <div class="category-badge">
                [ {{ $category_text }} ]
            </div>

            <p class="greeting">
                您好！您收到这封邮件是因为我们收到了您的<strong>{{ $category_text }}</strong>请求。<br>
                请输入以下验证码完成验证：
            </p>

            <div class="code-box">
                <div class="code-label">━━━━━ 验证码 ━━━━━</div>
                <div class="code">{{ $code }}</div>
                <div class="code-label">━━━━━━━━━━━━━━━━━</div>
            </div>

            <div class="info">
                <p>
                    ⚠️ <strong>重要提示：</strong>此验证码将在<strong>{{ $time }}分钟</strong>后过期。<br>
                    请在应用中输入此验证码以完成操作。
                </p>
            </div>

            <div class="divider"></div>

            <p class="warning">
                如果您没有发起此请求，请忽略此邮件。<br>
                请确保您在官方网站上输入敏感信息。
            </p>
        </div>

        <div class="email-footer">
            <div class="company">
                © {{ date('Y') }} ──── 保留所有权利 ────
            </div>
            <div class="unsubscribe">
                这是自动发送的邮件，请不要回复此邮件。
            </div>
        </div>
    </div>
</body>

</html>