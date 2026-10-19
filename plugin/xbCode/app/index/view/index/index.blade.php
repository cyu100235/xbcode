@php($installed = $installed ?? \plugin\xbCode\utils\FrameUtil::checked())
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $installed ? '积木云 xbCode' : '系统尚未安装 - 积木云 xbCode' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f6fa;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
        }

        .card {
            width: 100%;
            max-width: 560px;
            padding: 48px 40px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06);
            text-align: center;
        }

        .brand {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #111827;
        }

        .brand span {
            color: #6366f1;
        }

        .tagline {
            margin-top: 16px;
            font-size: 16px;
            line-height: 1.7;
            color: #4b5563;
        }

        .desc {
            margin-top: 8px;
            font-size: 14px;
            line-height: 1.8;
            color: #9ca3af;
        }

        .code {
            margin-top: 24px;
            padding: 14px 16px;
            font-family: 'Courier New', Consolas, monospace;
            font-size: 14px;
            line-height: 1.6;
            color: #6366f1;
            text-align: left;
            white-space: pre-wrap;
            word-break: break-all;
            background: #f7f8fc;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
        }

        .footer {
            margin-top: 24px;
            font-size: 12px;
            color: #9ca3af;
        }

        @media (max-width: 480px) {
            .card {
                padding: 36px 24px;
            }

            .brand {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <main class="card">
        <h1 class="brand">积木云 <span>xbCode</span></h1>
        @if($installed)
            <p class="tagline">插件化开发框架，让积木式开发更简单</p>
            <p class="desc">提供插件化开发基础能力，支持快速搭建后台与业务模块。</p>
        @else
            <p class="tagline">请在项目根目录执行以下命令完成安装</p>
            <pre class="code">php webman xb-install</pre>
        @endif
    </main>
    <footer class="footer">贵州积木云网络科技有限公司 · www.xbcode.net</footer>
</body>
</html>
