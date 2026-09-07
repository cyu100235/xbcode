<!doctype html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="/favicon.ico"/>
<title>{{ $title }}</title>
<style>
  :root{
    --bg:#07101F; --bg-2:#0B1A33;
    --brand:#58C4FF; --brand-2:#7E6BFF; --accent:#FFB547; --ok:#5EEAD4;
    --text:#E6EEFF; --muted:#94A7CC; --line:rgba(120,170,255,.22);
    --radius:20px;
  }
  *{box-sizing:border-box}
  html,body{margin:0;padding:0;height:100%}
  body{
    font-family:"Inter","HarmonyOS Sans SC","PingFang SC","Microsoft YaHei",system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
    color:var(--text);
    min-height:100%;
    display:flex;align-items:center;justify-content:center;
    background:
      radial-gradient(1000px 560px at 12% -10%, rgba(126,107,255,.28), transparent 60%),
      radial-gradient(900px 520px at 100% 10%, rgba(88,196,255,.26), transparent 60%),
      radial-gradient(700px 500px at 50% 120%, rgba(255,181,71,.14), transparent 60%),
      linear-gradient(180deg, var(--bg) 0%, var(--bg-2) 100%);
    -webkit-font-smoothing:antialiased;
    overflow:hidden;
  }
  .card{
    position:relative;
    width:min(680px, 92vw);
    padding:44px 44px 32px;
    border-radius:var(--radius);
    border:1px solid var(--line);
    background:linear-gradient(180deg, rgba(16,38,78,.55), rgba(7,16,31,.65));
    box-shadow:0 30px 120px -40px rgba(0,0,0,.75), 0 0 0 1px rgba(88,196,255,.10) inset, 0 0 60px rgba(88,196,255,.10);
    text-align:center;
  }
  .card::before{
    content:"";position:absolute;inset:-1px;border-radius:var(--radius);padding:1px;
    background:linear-gradient(135deg, rgba(88,196,255,.55), rgba(126,107,255,.35) 40%, rgba(255,181,71,.45) 100%);
    -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
    -webkit-mask-composite:xor; mask-composite:exclude;
    pointer-events:none;
  }
  .logo{
    margin:0 auto 22px;position:relative;width:76px;height:76px;border-radius:22px;
    background:conic-gradient(from 210deg at 62% 40%, #58C4FF 0deg, #7E6BFF 90deg, #FFB547 200deg, #5EEAD4 300deg, #58C4FF 360deg);
    box-shadow:0 12px 40px rgba(88,196,255,.28), 0 0 0 1px rgba(255,255,255,.08) inset;
    animation: float 6s ease-in-out infinite;
  }
  .logo::after{
    content:"";position:absolute;inset:10px;border-radius:16px;
    background:linear-gradient(180deg, rgba(7,16,31,.9), rgba(11,26,51,.78));
    backdrop-filter:blur(1px);
  }
  .logo::before{
    content:"XB";position:absolute;inset:0;display:grid;place-items:center;z-index:2;
    font-family:"JetBrains Mono",ui-monospace,Menlo,Consolas,monospace;
    font-weight:800;font-size:32px;color:#fff;letter-spacing:-.5px;
    text-shadow:0 1px 0 rgba(0,0,0,.4), 0 0 18px rgba(88,196,255,.55);
  }
  .brand{
    font-family:"JetBrains Mono",ui-monospace,Menlo,Consolas,monospace;
    font-size:14px;color:var(--muted);letter-spacing:.22em;text-transform:uppercase;margin-bottom:10px;
  }
  .brand b{color:#FFD49A;font-weight:700}
  h1{
    margin:0 0 16px;
    font-size:clamp(28px, 4.6vw, 46px);
    line-height:1.2;letter-spacing:-.01em;font-weight:800;
  }
  h1 .grad{
    background:linear-gradient(100deg, #FFFFFF 0%, #BEE9FF 35%, #7E6BFF 65%, #FFB547 100%);
    -webkit-background-clip:text;background-clip:text;color:transparent;
  }
  h1 .mono{
    display:inline-block;padding:4px 14px;margin:0 6px;border-radius:12px;
    border:1px solid rgba(120,200,255,.32);background:rgba(88,196,255,.08);
    font-family:"JetBrains Mono",ui-monospace,Menlo,Consolas,monospace;font-weight:700;
    font-size:.7em;vertical-align:.08em;letter-spacing:0;
    color:#DDF1FF;
  }
  .sub{color:var(--muted);font-size:15.5px;margin:0 0 26px}
  .sub b{color:#CFE4FF;font-weight:600}
  .row{
    display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-bottom:18px;
  }
  .pill{
    display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:999px;
    border:1px solid var(--line);background:rgba(255,255,255,.03);
    color:#C9D6F2;font-size:13px;
  }
  .pill i{width:6px;height:6px;border-radius:999px;background:var(--ok);box-shadow:0 0 0 4px rgba(94,234,212,.14);display:inline-block}
  .term{
    margin-top:8px;padding:14px 18px;border-radius:14px;background:#06101F;border:1px solid var(--line);
    font-family:"JetBrains Mono",ui-monospace,Menlo,Consolas,monospace;font-size:13px;color:#CFE4FF;
    text-align:left;overflow:auto;
  }
  .term .ok{color:#9EF5D8}
  .term .k{color:#B6A6FF}
  .term .c{color:#8FA3C9}
  .foot{
    margin-top:18px;color:var(--muted);font-size:12.5px;
  }
  .foot b{color:#E6EEFF}
  @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
  @media (prefers-reduced-motion:reduce){.logo{animation:none}}
  :focus-visible{outline:2px solid #58C4FF;outline-offset:2px;border-radius:6px}
</style>
</head>
<body>
  <main class="card" role="main" aria-label="{{ $name }} 欢迎页">
    <div class="logo" aria-hidden="true"></div>
    <div class="brand"><b>{{ $name }}</b> · Plugin · xbCode Framework</div>

    <h1>
      欢迎使用 <span class="mono">xbCode</span><br/>
      <span class="grad">{{ $title }} 插件已就绪</span>。
    </h1>
    <p class="sub">{{ $desc }}</p>

    <div class="foot">基于 <b>xbCode · 积木云微服务框架</b> · Plugin Skeleton</div>
  </main>
</body>
</html>