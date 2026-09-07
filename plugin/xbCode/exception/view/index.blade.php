<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>错误页面 - xbcode</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @keyframes floating {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(20px, 20px); }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        .container {
            position: relative;
            max-width: 600px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.5s ease-out;
        }

        .error-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s ease-in-out infinite;
            color: white;
            font-size: 48px;
            font-weight: bold;
            line-height: 80px;
            text-align: center;
        }

        h2 {
            font-size: 28px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
            text-align: center;
        }

        .content {
            margin: 25px 0;
        }

        .exception {
            background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            word-wrap: break-word;
            white-space: normal;
            color: #2d3748;
            font-size: 16px;
            line-height: 1.6;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(253, 203, 110, 0.3);
            border-left: 4px solid #e17055;
        }

        .message {
            background: #f7fafc;
            padding: 20px;
            border-radius: 12px;
            word-wrap: break-word;
            white-space: normal;
            color: #4a5568;
            font-size: 14px;
            line-height: 1.8;
            border: 1px solid #e2e8f0;
        }

        .message > div {
            margin: 8px 0;
            padding: 8px 12px;
            background: white;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
        }

        .button-group {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">!</div>
        <h2>出错了!</h2>
        <div class="content">
            <div class="exception">
                {{ $message }}
            </div>
            @if($debug)
            <div class="message">
                <div>{{ $file }}</div>
                <div>错误行：{{ $line }}</div>
            </div>
            @endif
        </div>
        <div class="button-group">
            <a href="/">返回首页</a>
        </div>
    </div>
</body>
</html>
