<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ</title>
</head>
<body>
    <div style="max-width: 800px; margin: 2rem auto; font-family: sans-serif;">
        <h1>マイページ</h1>
        <p>ログイン中の会員向けページです。</p>
        <ul>
            <li>名前: {{ auth()->user()->name }}</li>
            <li>メール: {{ auth()->user()->email }}</li>
        </ul>
        <a href="{{ route('alacarte') }}">アラカルトへ戻る</a>
    </div>
</body>
</html>
