<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カレンダー作成</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif; /* フォントを設定 */
        background-color: #b9a9a9ff; /* 背景色を設定 */
        color: #333; /* 文字色を設定 */
        padding: 20px; /* パディングを追加 */
    }
    h1 {
        text-align: center; /* タイトルを中央揃え */
        color: #962424ff; /* タイトルの色を設定 */
        margin-bottom: 20px; /* タイトルとフォームの間隔 */
        font-size: 2.5em; /* タイトルのフォントサイズを大きく */
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1); /* タイトルに影を追加 */
    }
    form {
        max-width: 400px; /* フォームの最大幅を設定 */
        margin: auto; /* フォームを中央に配置 */
        background-color: #fff; /* フォームの背景色 */
        padding: 20px; /* フォーム内のパディング */
        border-radius: 8px; /* 角を丸くする */
        box-shadow: 0 2px 10px rgba(0,0,0,0.1); /* ボックスシャドウ */
    }
    label {
        display: block; /* ラベルをブロック要素にする */
        margin-bottom: 10px; /* ラベルと入力の間隔 */
    }
    /* 年月タイトルのスタイル */
    .title {
        font-size: 24px; /* 文字サイズを大きくする */
        font-weight: bold; /* 強調表示 */
        text-align: left; /* 左揃え */
        color: #4CAF50; /* 緑系の爽やかな色 */
        background-color: #f0f8ff; /* 背景色を淡い青に */
        padding: 10px; /* 余白を追加 */
        border-radius: 8px; /* 角を丸くする */
    }
    button {
        display: block; /* ボタンをブロック要素にする */
        width: 100%; /* ボタンの幅を100%にする */
        padding: 10px; /* ボタンのパディング */
        background-color: #007bff; /* ボタンの背景色 */
        color: white; /* ボタンの文字色 */
        border: none; /* ボーダーなし */
        border-radius: 4px; /* ボタンの角を丸くする */
        cursor: pointer; /* カーソルをポインターに変更 */
    }
    button:hover {
        background-color: #0056b3; /* ホバー時の背景色変更 */
        transition: background-color 0.3s ease; /* ホバー時のアニメーション */
    }
    select {
        display: inline-block; /* セレクトボックスをインラインブロックにする */
        margin-right: 50px; /* セレクトボックスの間隔を調整 */
        color: #000508ff;
        font-family: Arial, sans-serif; /* フォントを設定 */
        font-size: 16px; /* 文字サイズを変更 */
        width: 150px;    /* 幅を調整 */
        height: 30px;    /* 高さを調整 */
        padding: 5px;    /* パディングを追加 */
        border: 1px solid #ccc; /* ボーダーを追加 */
    }
    div#output-section {
        margin-left: auto;
        margin-right: auto;
        padding: 10px; /* パディングを追加 */
        background-color: #ebe2e2ff; /* 出力セクションの背景色 */
        border: 1px solid #ddd; /* ボーダーを追加 */
        border-radius: 5px; /* 角を丸くする */
    }
    div#calendar-section {
        margin-left: auto;
        margin-right: auto;
        padding: 10px; /* パディングを追加 */
        background-color: #a5537aff; /* 背景色を設定 */
        border: 1px solid #ddd; /* ボーダーを追加 */
        border-radius: 5px; /* 角を丸くする */
    }
    /* 先頭行（日曜日）を赤に */
    table th:nth-child(1) {
        color: red;
        font-weight: bold; /* 文字を強調 */
    }

    /* 先頭行（土曜日）を青に */
    table th:nth-child(7) {
        color: blue;
        font-weight: bold; /* 文字を強調 */
    }
    table td:nth-child(1) {
        color: red;
        font-weight: bold;
        background-color: #ffe6e6; /* 日曜日の背景色（薄い赤） */
    }

    table td:nth-child(7) {
        color: blue;
        font-weight: bold;
        background-color: #e6f7ff; /* 土曜日の背景色（薄い青） */
    }
</style>
<body>
    <h1>カレンダー作成</h1>

    <form id="calendar-form" action="api.php" method="POST">
        <div class="title">年</div>
        <select id="year" name="year">
        <!-- 年範囲 -->
        <?php
            // 1950年から2099年までの年を選択できるようにする
            // まず、PHPのdate関数を使って現在の年を取得
            $currentYear = date("Y");
            for ($y = 1950; $y <= 2099; $y++) {
                if ($y == $currentYear) {
                    echo "<option value='$y' selected>$y</option>";
                } else {
                    echo "<option value='$y'>$y</option>";
                }
            }
        ?>
        </select>

        <br>
        <div class="title">月</div>
        <select id="month" name="month">
        <!-- 月範囲 -->
        <?php
            // 1月から12月までの範囲を生成
            for ($m = 1; $m <= 12; $m++) {
                $m2 = str_pad($m, 2, '0', STR_PAD_LEFT);
                if ($m2 == date("m")) {
                    echo "<option value='$m2' selected>$m2</option>";
                } else {
                    echo "<option value='$m2'>$m2</option>";
                }
            }
        ?>
        </select>

        <br><br>
        <button id="generate-calendar" type="button">作成します‼</button>
        <div id="output-section">
            <!-- ここに表示したいものを追加 -->
        </div>
    </form>

    <!-- カレンダーの表示セクション -->
    <div id="calendar-section">
        <!-- ここにカレンダーを表示 -->
        <script src="calendar.js"></script>
    </div>

</body>
</html>
